<?php

//******************************************************************************
//                                       DataController.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

require_once '../config/config.php';

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\yiiModels\YiiModelsConstants;

/**
 * CRUD actions for YiiDataModel
 * 
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDataModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DataController extends Controller {
    /**
     * Define the behaviors
     * 
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
     * search data (by variable, start date, end date). Used in the
     * experiment map visualisation (layer view)
     * @return mixed
     */
    public function actionSearchFromLayer() {
        $searchModel = new \app\models\yiiModels\DataSearchLayers();
        
        //1. get Variable uri list
        $variableModel = new \app\models\yiiModels\YiiVariableModel($pageSize = 500);
        $this->view->params["variables"] = $variableModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session['access_token']);
        
        if ($searchModel->load(Yii::$app->request->post())) {
            $scientificObjects = explode(",", Yii::$app->request->post()["agronomicalObjects"]);
            
            $toReturn["variable"] = $searchModel->variable;
            
            //2. For each given scientific object, get data            
            foreach ($scientificObjects as $scientificObject) {
                $agronomicalObject = [];
                $searchModel->object = $scientificObject;
                
                $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->post());
                
                /* Build array for highChart
                 * e.g : 
                 * {
                 *   "variable": "http:\/\/www.phenome-fppn.fr\/phenovia\/id\/variable\/v0000001",
                 *   "agronomicalObject": [
                 *          "uri": "http:\/\/www.phenome-fppn.fr\/phenovia\/2017\/o1028919",
                 *          "data": [["1,874809","2015-02-10"],
                 *                   ["2,313261","2015-03-15"]
                 *    ]
                 *  }]
                 * }
                 */
                $agronomicalObject["uri"] = $searchModel->object;
                foreach ($searchResult->getModels() as $model) {
                    $dataToSave = null;
                    $dataToSave[] = (strtotime($model->date))*1000;
                    $dataToSave[] = doubleval($model->value);
                    $agronomicalObject["data"][]= $dataToSave;
                }
                
                $toReturn["agronomicalObjects"][] = $agronomicalObject;
            }
            
            return $this->renderAjax('_form_data_graph', [
                        'model' => $searchModel,
                        'data' => $toReturn,
                   ]);
        } else {
            return $this->renderAjax('_form_data_graph', [
                        'model' => $searchModel
                   ]);
        }
    }
    
    /**
     * Prepare and show the index page of the data. Use the DataSearch class.
     * @see \app\models\yiiModels\DataSearch
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \app\models\yiiModels\DataSearch();
        
        //list of variables
        $variableModel = new \app\models\yiiModels\YiiVariableModel();
        $variables = $variableModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session['access_token']);
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;        
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[YiiModelsConstants::PAGE]--;
        }
        
        if (empty($searchParams["variable"])) {
            $key = $value = NULL;
            
            //The variable search parameter is required. 
            //If there is no variable, get the first variable uri.
            //SILEX:info
            //It is possible to use array_key_first instead of the following foreach, 
            //with PHP 7 >= 7.3.0
            //\SILEX:info
            foreach ($variables as $key => $value) {
                $searchModel->variable = $key;
                break;
            }
        }
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
               'dataProvider' => $searchResult,
               'variables' => $variables
            ]);
        }
    }
}