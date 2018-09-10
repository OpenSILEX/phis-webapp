<?php
//******************************************************************************
//                                       AcquisitionSessionController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 07 Sept, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\yiiModels\YiiDocumentModel;
use app\models\wsModels\WSConstants;
use app\models\wsModels\WSAcquisitionSession;
use app\components\helpers\SiteMessages;

require_once '../config/config.php';

/**
 * CRUD actions for Acquisition Session Metadata File
 * @see yii\web\Controller
 * @author Arnaud CHARLEROY <arnaud.charleroy@inra.fr>
 */
class AcquisitionSessionController extends Controller {
    
    /**
     * The name of the worksheet
     */
    CONST PHIS_SHEET_NAME = "HiddenPhis";
    
    /**
     *
     * @var string Type uri of the document file     
     */
    private $documentFileType;
    
    /**
     *
     * @var string Vector type uri 
     */
    private $vectorRdfType;
    
    /**
     *
     * @var string Retreive error
     */
    private $error;

    /**
     * Define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Set the right uris for robot field acquisition session metadata
     * @return Reponse
     */
    public function actionGenerateFieldRobot() {
        $this->vectorRdfType = Yii::$app->params['FieldRobot'];
        $this->documentFileType = Yii::$app->params['FieldRobotDocument'];

        return $this->generateFile();
    }

    /**
     * Set the right uris for uav acquisition session metadata
     * @return Reponse
     */
    public function actionGenerateUav() {
        $this->vectorRdfType = Yii::$app->params['UAV'];
        $this->documentFileType = Yii::$app->params['UAVDocument'];

        return $this->generateFile();
    }
    
    /**
     * Generated the session acquisition file 
     * @return Reponse
     */
    private function generateFile() {
        // get the last document and save it on the server
        $existingFilePath = $this->getTemplateFile();
        if ($this->error != null) {
            return $this->error;
        }
        // add data retreive from the ws
        $newFilePath = $this->addHiddenphisSheetData($existingFilePath);
        if ($this->error != null) {
            return $this->error;
        }
        // save the new file and send it to the user
        $this->sendTemplateFile($newFilePath);
        if ($this->error != null) {
            return $this->error;
        }
    }

    /**
     * Retreive the saved document linked to the document type required
     * @return mixed an error response or a string with the file path
     */
    private function getTemplateFile() {
        // 1. Get installation uri
        $infrastructureUri = substr(Yii::$app->params['baseURI'], 0, -1);
        
        // 2. Get the last acquisition template for the document type required
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel();
        $search["documentType"] = $this->documentFileType;
        $search["concernedItem"] = $infrastructureUri;
        //SILEX:info
        // Order is "desc" by default
        //\SILEX:info
        $wsResult = $documentModel->find($sessionToken, $search);
        
        // 3. Get the last acquisition template saved for the document type required
        if ($wsResult != null && isset($wsResult[0])) {
            $acquistionDocMetadata = $wsResult[0];
            $existingFilePath = $documentModel->getDocument($sessionToken, $acquistionDocMetadata->uri, $acquistionDocMetadata->format);
            //SILEX:info
            // Find a way to send a generic response
            //\SILEX:info
            if ($existingFilePath == WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
            // 4. Return the physical path of the file
            $realPath = str_replace(\config::path()['documentsUrl'], Yii::getAlias('@webroot/documents/') , $existingFilePath );
            return $realPath;
        } 
        
        // 4. Return an warning if no file present
        $this->error = $this->render(
            SiteMessages::SITE_WARNING_PAGE_ROUTE, [
                   SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                   SiteMessages::SITE_PAGE_MESSAGE => SiteMessages::CANT_FETCH_FILE_AQUI_SESS
            ]
        );
        
    }
    /**
     * 
     * @param type $existingFilePath the server physical path of the retrieved file 
     * @return string
     */
    private function addHiddenphisSheetData($existingFilePath) {
        // 1. Create save file path
        $filename = basename($existingFilePath);
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $newFilePath = Yii::getAlias('@webroot/documents/') . $file_name . '_with_' . self::PHIS_SHEET_NAME . '.' . $file_ext;
        
        // 2. Read existing file
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        // try if the file can be read
        try{
            $spreadsheet = $reader->load($existingFilePath);
        } catch (Exception $ex) {
            $this->error = $this->render(
                SiteMessages::SITE_WARNING_PAGE_ROUTE, [
                       SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                       SiteMessages::SITE_PAGE_MESSAGE => SiteMessages::CANT_READ_FILE
                ]
            );
            return;
        }
       
        // 3. Get information from the webservice
        /** @example for RobotField document type
         * [
         *   {
         *     "Installation": null,
         *     "GroupPlot_type": "http://www.phenome-fppn.fr/vocabulary/2017#Experiment",
         *     "GroupPlot_alias": "tes",
         *     "GroupPlot_uri": "http://www.phenome-fppn.fr/phis/PHS2018-1",
         *     "GroupPlot_species": "",
         *     "Pilot": "test.test@inra.fr"
         *   }
         * ]
         */
        $sessionToken = Yii::$app->session['access_token'];
        $wsAcquisitionSession = new WSAcquisitionSession();
        $fileMetadataByURI = $wsAcquisitionSession->getFileMetadataByURI($sessionToken, $this->vectorRdfType, [WSConstants::PAGE_SIZE => 100]);
        if (!is_string($fileMetadataByURI)) {
            if (isset($fileMetadataByURI[\app\models\wsModels\WSConstants::TOKEN])) {
                $this->error = $this->render(
                                        SiteMessages::SITE_ERROR_PAGE_ROUTE,
                                        [
                                            SiteMessages::SITE_PAGE_NAME =>  SiteMessages::NOT_CONNECTED,
                                            SiteMessages::SITE_PAGE_MESSAGE => $ex->getMessage()
                                        ]
                                    );
                return;
            } 
        } else {
            $this->error = $this->render(
                                    SiteMessages::SITE_ERROR_PAGE_ROUTE,
                                    [
                                        SiteMessages::SITE_PAGE_NAME => SiteMessages::ERROR_WHILE_FETCHING_DATA,
                                        SiteMessages::SITE_PAGE_MESSAGE => $ex->getMessage()
                                    ]
                                );
            return;
        }
        // 4. Save information in hiddenPhis Sheet
        // 4.1 Select sheet or create if not
        if(!$spreadsheet->sheetNameExists(self::PHIS_SHEET_NAME)){
            $HiddenPhisWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, self::PHIS_SHEET_NAME);
            // Create a new worksheet called "HiddenPhis" for example at the end 
            $spreadsheet->addSheet($HiddenPhisWorkSheet);
        }
        $spreadsheet->setActiveSheetIndexByName(self::PHIS_SHEET_NAME);
        $sheet = $spreadsheet->getActiveSheet();
        // 4.2 Fill this sheet with data from 'A1' cell
        /** 
         * @link https://phpspreadsheet.readthedocs.io/en/develop/topics/accessing-cells/#setting-a-range-of-cells-from-an-array 
         */
        $sheetData = [];
        $firstLine = true;
        foreach ($fileMetadataByURI as $metadata) {
            if ($firstLine) {
                $sheetData[] = array_keys((array) $metadata);
                $firstLine = false;
            }
            $sheetData[] = array_values((array) $metadata);
        }
        $sheet->fromArray($sheetData, null, "A1");

        // 4.3 Save modified worksheet
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        try{
            $writer->save($newFilePath);
            return $newFilePath;
        } catch (Exception $ex) {
            $this->error = $this->render(
                                SiteMessages::SITE_WARNING_PAGE_ROUTE, 
                                [
                                    SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                                    SiteMessages::SITE_PAGE_MESSAGE => $ex->getMessage()
                                ]
                            );
            return;
        }
    }

    /**
     * Send the file to the user.
     * @param string $newFilePath the path of the modified template
     * @return Response send a file to the user
     *                  or send an error
     */
    private function sendTemplateFile($newFilePath) {
        if (file_exists($newFilePath)) {
            Yii::$app->response->sendFile($newFilePath)->send();
            unlink($newFilePath);
        } else {
            $this->render(
                    SiteMessages::SITE_WARNING_PAGE_ROUTE, [
                            SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                            SiteMessages::SITE_PAGE_MESSAGE => SiteMessages::CANT_SEND_FILE
                        ]
            );
            return;
        }
    }
}