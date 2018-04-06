<?php

//******************************************************************************
//                                       VectorController.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject:implements the CRUD actions for the Vector model
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiVectorModel;

/**
 * CRUD actions for vector model
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiVectorModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class VectorController extends Controller {
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
     * get the vectors types
     * @return array list of the vectors types uris 
     * e.g. [
     *          "UAV",
     *          "Pot"
     *      ]
     */
    public function getVectorsTypes() {
        $model = new YiiVectorModel();
        
        $vectorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $vectorDevicesConcepts = $model->getVectorsTypes(Yii::$app->session['access_token']);
            if ($vectorDevicesConcepts === "token") {
                return "token";
            } else {
                $totalPages = $vectorDevicesConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];

                foreach ($vectorDevicesConcepts[\app\models\wsModels\WSConstants::DATA] as $vectorType) {
                    $vectorsTypes[] = explode("#", $vectorType->uri)[1];
                }
            }
        }
        
        return $vectorsTypes;
    }
    
    /**
     * get the vectors types (complete uri)
     * @return array list of the vectors types uris 
     * e.g. [
     *          "http://www.phenome-fppn.fr/vocabulary/2017#UAV",
     *          "http://www.phenome-fppn.fr/vocabulary/2017#Pot"
     *      ]
     */
    public function getVectorsTypesUris() {
        $model = new YiiVectorModel();
        
        $vectorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $vectorsConcepts = $model->getVectorsTypes(Yii::$app->session['access_token']);
            if ($vectorsConcepts === "token") {
                return "token";
            } else {
                $totalPages = $vectorsConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];
                foreach ($vectorsConcepts[\app\models\wsModels\WSConstants::DATA] as $vectorType) {
                    $vectorsTypes[] = $vectorType->uri;
                }
            }
        }
        
        return $vectorsTypes;
    }
    
    /**
     * generated the vector creation page
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiVectorModel();
        
        $vectorsTypes = $this->getVectorsTypes();
        if ($vectorsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $variableModel = new \app\models\yiiModels\YiiVariableModel();
        return $this->render('create', [
            'model' => $model,
            'vectorsTypes' => json_encode($vectorsTypes, JSON_UNESCAPED_SLASHES)
        ]);
    }
    
    /**
     * 
     * @param string $vectorType
     * @return string the complete vector type uri corresponding to the given 
     *                vector type
     *                e.g. http://www.phenome-fppn.fr/vocabulary/2017#UAV
     */
    private function getVectorTypeCompleteUri($vectorType) {
        $vectorsTypes = $this->getVectorsTypesUris();
        foreach ($vectorsTypes as $sensorTypeUri) {
            if (strpos($sensorTypeUri, $vectorType)) {
                return $sensorTypeUri;
            }
        }
        return null;
    }
    
    /**
     * create the given vectors
     * @return string the json of the creation return
     */
    public function actionCreateMultipleVectors() {
        $vectors = Yii::$app->request->post()["vectors"];
        $sessionToken = Yii::$app->session['access_token'];
        if (count($vectors) > 0) {
            $vectorsGraph = Yii::$app->params['baseURI'] . "vectors";
            //needs to insert sensors. 
            $triplets = null;
            
            foreach ($vectors as $vector) {
                $tripletsGroup = null;
                //1. triplet type
                $type = null;
                $type["s"] = "?";
                $type["p"] = "rdf:type";
                $type["o_type"] = "uri";
                $type["o"] = $this->getVectorTypeCompleteUri($vector[2]);
                $type["g"] = $vectorsGraph;
                $tripletsGroup[] = $type;
                
                //2. triplet alias
                $alias = null;
                $alias["s"] = "?";
                $alias["p"] = "rdfs:label";
                $alias["o_type"] = "literal";
                $alias["o"] = $vector[1];
                $alias["g"] = $vectorsGraph;
                $tripletsGroup[] = $alias;
                
                //3. triplet brand
                $brand = null;
                $brand["s"] = "?";
                $brand["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#hasBrand";
                $brand["o_type"] = "literal";
                $brand["o"] = $vector[3];
                $brand["g"] = $vectorsGraph;
                $tripletsGroup[] = $brand;
                
                //5. (optional) triplet inServiceDate
                if ($vector[5] !== "") {
                    $inServiceDate = null;
                    $inServiceDate["s"] = "?";
                    $inServiceDate["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#inServiceDate";
                    $inServiceDate["o_type"] = "literal";
                    $inServiceDate["o"] = $vector[5];
                    $inServiceDate["g"] = $vectorsGraph;
                    $tripletsGroup[] = $inServiceDate;
                }
                
                //6. (optional) triplet dateOfPurchase
                if ($vector[4] !== "") {
                    $dateOfPurchase = null;
                    $dateOfPurchase["s"] = "?";
                    $dateOfPurchase["p"] = "http://www.phenome-fppn.fr/vocabulary/2017#dateOfPurchase";
                    $dateOfPurchase["o_type"] = "literal";
                    $dateOfPurchase["o"] = $vector[4];
                    $dateOfPurchase["g"] = $vectorsGraph;
                    $tripletsGroup[] = $dateOfPurchase;
                }
                $triplets[] = $tripletsGroup;
            }
            
            $vectorModel = new YiiVectorModel();
            $insertionResult = $vectorModel->createVectors($sessionToken, $triplets);
            
            return json_encode($insertionResult, JSON_UNESCAPED_SLASHES); 
        }
        return true;
    }
    
    /**
     * Search a vector by uri.
     * @param String $uri searched vector's uri
     * @return mixed YiiSensorModel : the searched vector
     *               "token" if the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $vectorModel = new YiiVectorModel();
        $requestRes = $vectorModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $vectorModel;
        } else if(isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * list all vectors
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \app\models\yiiModels\VectorSearch();
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);
        
        if (is_string($searchResult)) {
            return $this->render('/site/error', [
                    'name' => 'Internal error',
                    'message' => $searchResult]);
        } else if (is_array($searchResult) && isset($searchResult["token"])) { //user must log in
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
                'dataProvider' => $searchResult
            ]);
        }
    }
    
    /**
     * @action Displays a single vector model
     * @return mixed
     */
    public function actionView($id) {
        $res = $this->findModel($id);
        
        if ($res === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {            
            return $this->render('view', [
                'model' => $res,
            ]);
        }
        
    }
}