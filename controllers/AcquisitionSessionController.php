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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


/**
 * CRUD actions for AcquisitionSession
 * @see yii\web\Controller
 * @author Arnaud CHARLEROY <arnaud.charleroy@inra.fr>
 */
class AcquisitionSessionController extends Controller {
    CONST UAV = "uav";
    CONST PHENOMOBILE = "phenomobile";
    
    
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
        return $this->generate(self::PHENOMOBILE);
        
    }
    
    /**
     * generated the vector creation page
     * @return mixed
     */
    public function actionGenerateUav() {
        return $this->generate(self::UAV);
    }

    /**
     * generated the vector creation page
     * @return mixed
     */
    private function generate($string) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel();
        
        //calls search document to get metadata by type
        $search["documentType"] = $type;
        $res = $documentModel->find($sessionToken, $search);
        // get the last document
        // save it
        // modify it
        $existingFilePath = Yii::getAlias('@webroot/documents/AcquisitionSessionFiles/Meta_session_UAV_1.7.xlsx');
        $newFilePath = Yii::getAlias('@webroot/documents/AcquisitionSessionFiles/Meta_session_UAV_1.7_test.xlsx');
        // call ws depend of the pass parameter
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
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
        }]',true);
        $spreadsheet->setActiveSheetIndexByName("HiddenPhis");
        $sheet = $spreadsheet->getActiveSheet();
        $sheetData =[];
        $firstLine = true;
        foreach ($array_metadata as $metadata) {
            if($firstLine){
                $sheetData[] = array_keys($metadata);
                $firstLine = false;
            }
            $sheetData[] = array_values($metadata);
        }
        $sheet->fromArray($sheetData, null, "A1" );
     
        
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($newFilePath);
        exit;
    }
    

}
