<?php
//******************************************************************************
//                                       VectorController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 25 Aug, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\yiiModels\YiiDocumentModel;
use app\models\wsModels\WSConstants;

require_once '../config/config.php';

/**
 * CRUD actions for AcquisitionSession
 * @see yii\web\Controller
 * @author Arnaud CHARLEROY <arnaud.charleroy@inra.fr>
 */
class AcquisitionSessionController extends Controller {

    CONST UAV_URI_PART = "AcquisitionSessionUAV";
    CONST PHENOMOBILE_URI_PART = "AcquisitionSessionPhenomobile";

    /**
     * define the behaviors
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
     * generated the vector creation page
     * @return mixed
     */
    public function actionGeneratePhenomobile() {
        return $this->generateFile(self::PHENOMOBILE_URI_PART);
    }

    /**
     * generated the vector creation page
     * @return mixed
     */
    public function actionGenerateUav() {
        return $this->generateFile(self::UAV_URI_PART);
    }

    /**
     * generated the vector creation page
     * @return mixed
     */
    private function generateFile($documentType) {
        // get the last document and save it on the
        $existingFilePath = $this->getTemplateFile($documentType);
        // modify it
        $newFilePath = $this->addHiddenphisSheetData($existingFilePath);

        $this->sendTemplateFile($newFilePath);
    }

    private function getTemplateFile($documentType) {
        $infrastructureUri = substr(Yii::$app->params['baseURI'], 0, -1);
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel();

        //Get document's types
        $documentsTypes = $documentModel->findDocumentsTypes($sessionToken);

        if (is_string($documentsTypes)) {
            return $this->render(
                            '/site/error', [
                        'name' => 'Internal error',
                        'message' => $documentsTypes
                            ]
            );
        } else if (is_array($documentsTypes) && isset($documentsTypes["token"])) {
            return $this->redirect(Yii::$app->urlManager->createUrl(WSConstants::TOKEN));
        } else {


            $documentTypeURIArray = preg_grep("/$documentType/", $documentsTypes);
            //SILEX:info
            // For example, you can get this kind of result below, so you need to sort the array
            // before use it
            // array(1) { [1]=> string(72) "http://www.phenome-fppn.fr/vocabulary/2017#AcquisitionSessionUAVDocument" }
            //SILEX:info 
            // get right array index
            sort($documentTypeURIArray);
            //calls search document to get metadata by type
            $search["documentType"] = $documentTypeURIArray[0];
            $search["concernedItem"] = $infrastructureUri;
            $wsResult = $documentModel->find($sessionToken, $search);

            if ($wsResult != null && isset($wsResult[0])) {
                $acquistionDocMetadata = $wsResult[0];
                $existingFilePath = $documentModel->getDocument($sessionToken, $acquistionDocMetadata->uri, $acquistionDocMetadata->format);
                if ($existingFilePath == WSConstants::TOKEN) {
                    return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                }
                $realPath = str_replace(\config::path()['documentsUrl'], Yii::getAlias('@webroot/documents/') , $existingFilePath );
                return $realPath;
            } else {
                return $this->render(
                                '/site/error', [
                            'name' => 'Internal error',
                            'message' => "Can't fetch the file"
                                ]
                );
            }
        }
    }

    private function addHiddenphisSheetData($existingFilePath) {
        $filename = basename($existingFilePath);
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $newFilePath = Yii::getAlias('@webroot/documents/') . $file_name . '_with_hiddenPhis.' . $file_ext;
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        // call ws depend of the pass parameter
        $spreadsheet = $reader->load($existingFilePath);
        $array_metadata = json_decode('[{
            "Installation": "value",
            "GroupPlot_type": "value",
            "GroupPlot_uri": "value",
            "GroupPlot_alias": "value",
            "GroupPlot_species": "value",
            "Pilot": "value",
            "Camera_type": "value",
            "Camera_uri": "value",
            "Camera_alias": "value",
            "Vector_type": "value",
            "Vector_uri": "value",
            "Vector_alias": "value",
            "RadiometricTarget_uri": "value",
            "RadiometricTarget_alias": "value"
        }]', true);
        $spreadsheet->setActiveSheetIndexByName("HiddenPhis");
        $sheet = $spreadsheet->getActiveSheet();
        $sheetData = [];
        $firstLine = true;
        foreach ($array_metadata as $metadata) {
            if ($firstLine) {
                $sheetData[] = array_keys($metadata);
                $firstLine = false;
            }
            $sheetData[] = array_values($metadata);
        }
        $sheet->fromArray($sheetData, null, "A1");

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
         $saved = true;
        try{
            $writer->save($newFilePath);
             return $newFilePath;
        } catch (Exception $ex) {
            return $this->render(
                        '/site/error',
                        [
                            'name' => 'Internal error',
                            'message' => $ex->getMessage()
                        ]
                    );
        }
    }

    private function sendTemplateFile($newFilePath) {
        if (file_exists($newFilePath)) {
            Yii::$app->response->sendFile($newFilePath)->send();
            unlink($newFilePath);
        } else {
            return $this->render(
                    '/site/error', 
                    [
                        'name' => 'Internal error',
                        'message' => "Can't send the file"
                    ]
            );
        }
    }

}
