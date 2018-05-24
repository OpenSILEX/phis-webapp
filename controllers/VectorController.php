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
use app\models\yiiModels\UserSearch;

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
        
        $usersModel = new \app\models\yiiModels\YiiUserModel();
        $usersMails = $usersModel->getUsersMails(Yii::$app->session['access_token']);
        
        return $this->render('create', [
            'model' => $model,
            'vectorsTypes' => json_encode($vectorsTypes, JSON_UNESCAPED_SLASHES),
            'users' => json_encode($usersMails)
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
        $vectors = json_decode(Yii::$app->request->post()["vectors"]);
        $sessionToken = Yii::$app->session['access_token'];
        
        $vectorsUris = null;
        if (count($vectors) > 0) {
            foreach ($vectors as $vector) {
                $vectorModel = new YiiVectorModel();
                $vectorModel->rdfType = $this->getVectorTypeCompleteUri($vector[2]);
                $vectorModel->label = $vector[1];
                $vectorModel->brand = $vector[3];
                $vectorModel->inServiceDate = $vector[6];
                $vectorModel->personInCharge = $vector[7];
                if ($vector[4] !== "") {
                    $vectorModel->serialNumber = $vector[4];
                }
                if ($vector[5] != "") {
                    $vectorModel->dateOfPurchase = $vector[5];
                }

                $forWebService[] = $vectorModel->attributesToArray();
                $insertionResult = $vectorModel->insert($sessionToken, $forWebService);
                
                $vectorsUris[] = $insertionResult->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES}[0];
            }
            
            return json_encode($vectorsUris, JSON_UNESCAPED_SLASHES); 
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
    
    /**
     * 
     * @param array $vectorsTypes
     * @return arra list of the vectors types
     */
    private function vectorsTypesToMap($vectorsTypes) {
        $toReturn;
        foreach($vectorsTypes as $type) {
            $toReturn["http://www.phenome-fppn.fr/vocabulary/2017#" . $type] = $type;
        }
        
        return $toReturn;
    }
    
    /**
     * 
     * @param mixed $users persons list
     * @return ArrayHelper list of the persons 'email' => 'email'
     */
    private function usersToMap($users) {
        if ($users !== null) {
            return \yii\helpers\ArrayHelper::map($users, 'email', 'email');
        } else {
            return null;
        }
    }
    
    /**
     * update a vector
     * @param string $id uri of the vector to update
     * @return mixed the page to show
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiVectorModel();
        $model->uri = $id;
        
        //if the form is complete, try to update vector
        if ($model->load(Yii::$app->request->post())) {
            
            $forWebService[] = $model->attributesToArray();
            $requestRes = $model->update($sessionToken, $forWebService);
            
            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $model->uri]);
            }
        } else {
            $model = $this->findModel($id);
            
            //list of vector's types
            $vectorsTypes = $this->getVectorsTypes();
            if ($vectorsTypes === "token") {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
        
            //list of users
            $searchUserModel = new UserSearch();
            $users = $searchUserModel->find($sessionToken, []);
            
            return $this->render('update', [
                'model' => $model,
                'types' => $this->vectorsTypesToMap($vectorsTypes),
                'users' => $this->usersToMap($users)
            ]);
        }
    }
}
