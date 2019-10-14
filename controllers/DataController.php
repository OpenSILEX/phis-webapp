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
    
    /**
     * Download a csv corresponding to the search params of the index view of the data search.
     * @return the csv file.
     */
    public function actionDownloadCsv() {
        $searchModel = new \app\models\yiiModels\DataSearch();
        if (isset($_GET['model'])) {
            $searchParams = $_GET['model'];
            $searchModel->variable = $searchParams["variable"];
            $searchModel->date = isset($searchParams["date"]) ? $searchParams["date"] : null;
            $searchModel->object = isset($searchParams["object"]) ? $searchParams["object"] : null;
            $searchModel->provenance = isset($searchParams["provenance"]) ? $searchParams["provenance"] : null;
        }
        
        // Set page size to 400000 for better performances
        $searchModel->pageSize = 400000;
        
        //get all the data (if multiple pages) and write them in a file
        $serverFilePath = \config::path()['documentsUrl'] . "AOFiles/exportedData/" . time() . ".csv";
        
        $headerFile = "variable URI" . Yii::$app->params['csvSeparator'] .
                      "variable" . Yii::$app->params['csvSeparator'] .
                      "date" . Yii::$app->params['csvSeparator'] .
                      "value" . Yii::$app->params['csvSeparator'] .
                      "object URI" . Yii::$app->params['csvSeparator'] . 
                      "object" . Yii::$app->params['csvSeparator'] . 
                      "provenance URI" . Yii::$app->params['csvSeparator'] . 
                      "provenance" . Yii::$app->params['csvSeparator'] . 
                      "\n";
        file_put_contents($serverFilePath, $headerFile);

        $allLinesStringToWrite = "";        
        $totalPage = 1;
        for ($i = 0; $i < $totalPage; $i++) {
            //1. call service for each page
            $searchParams["page"] = $i;

            $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);

            //2. write in file
            $models = $searchResult->getmodels();
            foreach ($models as $model) {
                $stringToWrite = $model->variable->uri . Yii::$app->params['csvSeparator'] . 
                                 $model->variable->label . Yii::$app->params['csvSeparator'] . 
                                 $model->date . Yii::$app->params['csvSeparator'] .
                                 $model->value . Yii::$app->params['csvSeparator'];
                $stringToWrite .= isset($model->object) ? $model->object->uri . Yii::$app->params['csvSeparator'] : "" . Yii::$app->params['csvSeparator'];

                $objectLabels = "";
                if (isset($model->object)) {
                    foreach ($model->object->labels as $label) {
                        $objectLabels .= $label . " ";
                    }
                }
                
                $stringToWrite .= $objectLabels . Yii::$app->params['csvSeparator'] .
                                  $model->provenance->uri . Yii::$app->params['csvSeparator'] .
                                  $model->provenance->label . Yii::$app->params['csvSeparator'] . 
                                 "\n";
                $allLinesStringToWrite .= $stringToWrite;
                
            }
            
            $totalPage = intval($searchModel->totalPages);
        }
        file_put_contents($serverFilePath, $allLinesStringToWrite, FILE_APPEND);
        Yii::$app->response->sendFile($serverFilePath); 
    }
}