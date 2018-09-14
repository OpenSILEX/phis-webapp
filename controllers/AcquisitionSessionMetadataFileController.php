<?php
//******************************************************************************
//                  AcquisitionSessionMetadataFileController.php
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
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class AcquisitionSessionMetadataFileController extends Controller {
    
    /**
     * The name of the worksheet which will be modified
     */
    const PHIS_SHEET_NAME = "HiddenPhis";
    
    /**
     * The params label for robot field document
     * @see config/params.php
     */
    const FIELD_ROBOT_DOCUMENT_LABEL =  "AcquisitionSessionPhenomobileDocument";
    
    /**
     * The params label for uav document
     * @see config/params.php
     */
    const UAV_DOCUMENT_LABEL =  "AcquisitionSessionUAVDocument";
    
    /**
     * The params label for uav vector
     * @see config/params.php
     */
    const UAV_VECTOR_LABEL =  "UAV";
    
    /**
     * The params label for field robot
     * @see config/params.php
     */
    const FIELD_ROBOT_VECTOR_LABEL =  "FieldRobot";
    
    /**
     * The type of the document
     * @example http://www.phenome-fppn.fr/vocabulary/2017#AcquisitionSessionUAVDocument  
     * @var string    
     */
    private $documentType;
    
    /**
     * The type of the vector required
     * @example http://www.phenome-fppn.fr/vocabulary/2017#AcquisitionSessionPhenomobileDocument 
     * @var string 
     */
    private $vectorType;
    
    /**
     * This variable store the lasted error 
     * which has occurred
     * @var string 
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
    public function actionGenerateFieldRobotMetadataFile() {
        $this->vectorType = Yii::$app->params[self::FIELD_ROBOT_VECTOR_LABEL];
        $this->documentType = Yii::$app->params[self::FIELD_ROBOT_DOCUMENT_LABEL];

        return $this->generateMetadataFile();
    }

    /**
     * Set the right uris for uav acquisition session metadata
     * @return Reponse
     */
    public function actionGenerateUavMetadataFile() {
        $this->vectorType = Yii::$app->params[self::UAV_VECTOR_LABEL];
        $this->documentType = Yii::$app->params[self::UAV_DOCUMENT_LABEL];

        return $this->generateMetadataFile();
    }
    
    /**
     * Generate the  acquisition session file 
     * @return Reponse
     */
    private function generateMetadataFile() {
        // Get the last document and save it on the server
        $existingFilePath = $this->getMetadataFileTemplate();
        if ($this->error != null) {
            return $this->error;
        }
        // Add data retreive from the ws to the worksheet choosen
        $newFilePath = $this->addHiddenphisSheetData($existingFilePath);
        if ($this->error != null) {
            return $this->error;
        }
        // Save the new file and send it to the user
        $this->sendMetadataFileTemplate($newFilePath);
        if ($this->error != null) {
            return $this->error;
        }
    }

    /**
     * Retreive the latest saved document which matches
     * with the choosen document type
     * @return mixed null
     *               or a string with the saved document path
     */
    private function getMetadataFileTemplate() {
        //SILEXinfo
        // A service will be created to retreive specific
        // infrastructure informations
        //\SILEX:info
        // 1. Get installation uri
        $infrastructureUri = substr(Yii::$app->params['baseURI'], 0, -1);
        
        // 2. Get the last acquisition template for the document type required
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel();
        $search["documentType"] = $this->documentType;
        $search["concernedItem"] = $infrastructureUri;
        
        $wsResult = $documentModel->find($sessionToken, $search);
        
        // 3. Get the lastest acquisition session template saved
        //    for the required document type
        //SILEX:info
        // sortByDate is "desc" by default
        // $search["sortByDate"] = "desc"; can be used but it's not required
        // in this case
        //\SILEX:info
        if ($wsResult != null && isset($wsResult[0])) {
            $acquistionDocMetadata = $wsResult[0];
            $existingFilePath = $documentModel->getDocument($sessionToken, $acquistionDocMetadata->uri, $acquistionDocMetadata->format);
            //SILEX:conception
            // Find a way to send a generic response when the user is disconnected
            //\SILEX:conception
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
     * Put data retreived from the ws into the choosen worksheet
     * @param string $existingFilePath the server physical path of the retrieved file
     * @example /var/www/html/phis-webapp/documents/excelfilev
     * @return string $newFilePath is the absolute path of the modified file, for example
     *                            /var/www/html/phis-webapp/documents/excelfile_with_HiddenPhis.xlsx
     */
    private function addHiddenphisSheetData($existingFilePath) {
        // 1. Create save file path
        $filename = basename($existingFilePath);
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $newFilePath = Yii::getAlias('@webroot/documents/') . $file_name . '_with_' . self::PHIS_SHEET_NAME . '.' . $file_ext;
        
        // 2. Read existing file
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        // Try if the file can be read
        try{
            $spreadsheet = $reader->load($existingFilePath);
        } catch (Exception $ex) {
            $this->error = $this->render(
                SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                       SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                       SiteMessages::SITE_PAGE_MESSAGE => SiteMessages::CANT_READ_FILE
                ]
            );
            return;
        }
       
        // 3. Get information from the webservice
        /** @example for Robot Field document type
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
        $fileMetadataByURI = $wsAcquisitionSession->getFileMetadataByURI($sessionToken, $this->vectorType, [WSConstants::PAGE_SIZE => 100]);
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
        
        // 4. Save information in the worksheet
        // 4.1 Select sheet or create if not
        if(!$spreadsheet->sheetNameExists(self::PHIS_SHEET_NAME)){
            $HiddenPhisWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, self::PHIS_SHEET_NAME);
            // Create a new worksheet called "HiddenPhis" for example at the end of the file
            $spreadsheet->addSheet($HiddenPhisWorkSheet);
        }
        $spreadsheet->setActiveSheetIndexByName(self::PHIS_SHEET_NAME);
        $sheet = $spreadsheet->getActiveSheet();
        
        // 4.2 Fill this sheet with data from 'A1' cell
        /** 
         * @link https://phpspreadsheet.readthedocs.io/en/develop/topics/accessing-cells/#setting-a-range-of-cells-from-an-array 
         * @example 
         * [
         *     ['Installation', 'GroupPlot_type', 'GroupPlot_alias', 'GroupPlot_uri'],
         *     [null, "http://www.phenome-fppn.fr/vocabulary/2017#Experiment", "tes",  "http://www.phenome-fppn.fr/phis/PHS2018-1"]
         * ]
         */
        $sheetData = [];
        $firstLine = true;
        foreach ($fileMetadataByURI as $metadata) {
            // the headers (keys of associative array)
            if ($firstLine) {
                $sheetData[] = array_keys((array) $metadata);
                $firstLine = false;
            }
            $sheetData[] = array_values((array) $metadata);
        }
        // fill the worksheet
        $sheet->fromArray($sheetData, null, "A1");

        // 4.3 Save modified document
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        try{
            $writer->save($newFilePath);
        } catch (Exception $ex) {
            $newFilePath = null;
            $this->error = $this->render(
                                SiteMessages::SITE_ERROR_PAGE_ROUTE, 
                                [
                                    SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                                    SiteMessages::SITE_PAGE_MESSAGE => $ex->getMessage()
                                ]
                            );
        }
        return $newFilePath;
    }

    /**
     * Send the file to the user.
     * @param string $newFilePath the path of the modified template
     * @return Response send a file to the user
     *                  or send an error
     */
    private function sendMetadataFileTemplate($newFilePath) {
        if (file_exists($newFilePath)) {
            Yii::$app->response->sendFile($newFilePath)->send();
            unlink($newFilePath);
        } else {
            $this->render(
                    SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                            SiteMessages::SITE_PAGE_NAME =>  SiteMessages::INTERNAL_ERROR,
                            SiteMessages::SITE_PAGE_MESSAGE => SiteMessages::CANT_SEND_FILE
                        ]
            );
            return;
        }
    }
}