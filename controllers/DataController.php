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
        $searchModel = new \app\models\yiiModels\DataSearch();
        
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
                    $toReturn["agronomicalObjects"][] = $agronomicalObject;
                }
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
}