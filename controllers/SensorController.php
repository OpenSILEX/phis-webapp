<?php

//******************************************************************************
//                                       SensorController.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: implements the CRUD actions for the Sensor model
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiSensorModel;

/**
 * CRUD actions for SensorModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiSensorModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class SensorController extends Controller {
        
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
     * get the sensors types (complete uri)
     * @return array list of the sensors types uris 
     * e.g. [
     *          "http://www.phenome-fppn.fr/vocabulary/2017#RGBImage",
     *          "http://www.phenome-fppn.fr/vocabulary/2017#HemisphericalImage"
     *      ]
     */
    public function getSensorsTypesUris() {
        $model = new YiiSensorModel();
        
        $sensorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $sensingDevicesConcepts = $model->getSensorsTypes(Yii::$app->session['access_token']);
            if ($sensingDevicesConcepts === "token") {
                return "token";
            } else {
                $totalPages = $sensingDevicesConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];
                foreach ($sensingDevicesConcepts[\app\models\wsModels\WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[] = $sensorType->uri;
                }
            }
        }
        
        
        return $sensorsTypes;
    }
    
    /**
     * get the sensors types
     * @return array list of the sensors types uris 
     * e.g. [
     *          "RGBImage",
     *          "HemisphericalImage"
     *      ]
     */
    public function getSensorsTypes() {
        $model = new YiiSensorModel();
        
        $sensorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $sensingDevicesConcepts = $model->getSensorsTypes(Yii::$app->session['access_token']);
            if ($sensingDevicesConcepts === "token") {
                return "token";
            } else {
                $totalPages = $sensingDevicesConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];

                foreach ($sensingDevicesConcepts[\app\models\wsModels\WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[] = explode("#", $sensorType->uri)[1];
                }
            }
        }
        
        
        return $sensorsTypes;
    }
    
    /**
     * generated the sensor creation page
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $sensorModel = new YiiSensorModel();
        
        $sensorsTypes = $this->getSensorsTypes();
        if ($sensorsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $variableModel = new \app\models\yiiModels\YiiVariableModel();
        $variables = $variableModel->getVariablesUriAndAlias();
        return $this->render('create', [
            'model' => $sensorModel,
            'sensorsTypes' => json_encode($sensorsTypes, JSON_UNESCAPED_SLASHES),
            'variables' => json_encode($variables, JSON_UNESCAPED_SLASHES)
        ]);
    }
    
    /**
     * 
     * @param string $sensorType
     * @return string the complete sensor type uri corresponding to the given 
     *                sensor type
     *                e.g. http://www.phenome-fppn.fr/vocabulary/2017#RGBImage
     */
    private function getSensorTypeCompleteUri($sensorType) {
        $sensorsTypes = $this->getSensorsTypesUris();
        foreach ($sensorsTypes as $sensorTypeUri) {
            if (strpos($sensorTypeUri, $sensorType)) {
                return $sensorTypeUri;
            }
        }
        return null;
    }
    
    /**
     * create the given sensors
     * @return string the json of the creation return
     */
    public function actionCreateMultipleSensors() {
        $sensors = Yii::$app->request->post()["sensors"];
        $sessionToken = Yii::$app->session['access_token'];
        if (count($sensors) > 0) {
            $sensorsGraph = Yii::$app->params['baseURI'] . "sensors";
            //needs to insert sensors. 
            $triplets = null;
            
            foreach ($sensors as $sensor) {
                $tripletsGroup = null;
                //1. triplet type
                $type = null;
                $type["s"] = "?";
                $type["p"] = "rdf:type";
                $type["o_type"] = "uri";
                $type["o"] = $this->getSensorTypeCompleteUri($sensor[2]);
                $type["g"] = $sensorsGraph;
                $tripletsGroup[] = $type;
                
                //2. triplet alias
                $alias = null;
                $alias["s"] = "?";
                $alias["p"] = "rdfs:label";
                $alias["o_type"] = "literal";
                $alias["o"] = $sensor[1];
                $alias["g"] = $sensorsGraph;
                $tripletsGroup[] = $alias;
                
                //3. triplet brand
                $brand = null;
                $brand["s"] = "?";
                $brand["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#hasBrand";
                $brand["o_type"] = "literal";
                $brand["o"] = $sensor[3];
                $brand["g"] = $sensorsGraph;
                $tripletsGroup[] = $brand;
                
                //5. (optional) triplet inServiceDate
                if ($sensor[4] !== "") {
                    $inServiceDate = null;
                    $inServiceDate["s"] = "?";
                    $inServiceDate["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#inServiceDate";
                    $inServiceDate["o_type"] = "literal";
                    $inServiceDate["o"] = $sensor[4];
                    $inServiceDate["g"] = $sensorsGraph;
                    $tripletsGroup[] = $inServiceDate;
                }
                
                //6. (optional) triplet dateOfPurchase
                if ($sensor[5] !== "") {
                    $dateOfPurchase = null;
                    $dateOfPurchase["s"] = "?";
                    $dateOfPurchase["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#dateOfPurchase";
                    $dateOfPurchase["o_type"] = "literal";
                    $dateOfPurchase["o"] = $sensor[5];
                    $dateOfPurchase["g"] = $sensorsGraph;
                    $tripletsGroup[] = $dateOfPurchase;
                }
                
                //7. (optional) triplet dateOfLastCalibration
                if ($sensor[6] !== "") {
                    $dateOfLastCalibration = null;
                    $dateOfLastCalibration["s"] = "?";
                    $dateOfLastCalibration["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#dateOfLastCalibration";
                    $dateOfLastCalibration["o_type"] = "literal";
                    $dateOfLastCalibration["o"] = $sensor[6];
                    $dateOfLastCalibration["g"] = $sensorsGraph;
                    
                    $tripletsGroup[] = $dateOfLastCalibration;
                }
                $triplets[] = $tripletsGroup;
            }
            
            $sensorModel = new YiiSensorModel();
            $insertionResult = $sensorModel->createSensors($sessionToken, $triplets);
            
            return json_encode($insertionResult, JSON_UNESCAPED_SLASHES); 
        }
        return true;
    }
}
