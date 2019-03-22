<?php

//******************************************************************************
//                                       DataAnalysisController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 feb 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\models\yiiModels\DataAnalysisAppSearch;

include_once '../config/web_services.php';

/**
 * Implements the controller for available statistical and visualisation programs
 * @see yii\web\Controller
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisController extends \yii\web\Controller {
    /**
     * Constants used to parse configuration file
     */
    const INTEGRATED_FUNCTIONS = "integratedFunctions";
    const FORM_PARAMETERS = "formParameters";
    
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
                ]
            ]
        ];
    }

    /**
     * List all R apps
     * @return mixed
     */
    public function actionIndex($integrated = false) {

        $searchModel = new DataAnalysisAppSearch();

        $searchParams = Yii::$app->request->queryParams;

        $searchResult = $searchModel->search($searchParams);
        if (empty($searchResult)) {
            return $this->render('error', [
                        'message' => 'No application available.'
            ]);
        } else {
            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        'integrated' => $integrated
                            ]
            );
        }
    }

    /**
     * Show standalone Demo R app
     * @return type
     */
    public function actionViewDemo() {
        $searchModel = new DataAnalysisAppSearch();
        $appDemoInformation = $searchModel->getAppDemoInformation();
        if (!empty($appDemoInformation)) {
            $this->redirect($appDemoInformation[DataAnalysisAppSearch::APP_INDEX_HREF]);
        } else {
            return $this->render('error', [
                        'message' => 'Demo application not available.'
            ]);
        }
    }

    /**
     * Show standalone Demo R app
     * @return type
     */
    public function actionView() {
        $searchParams = Yii::$app->request->queryParams;
        return $this->render('iframe-view', [
                    'appUrl' => $searchParams["url"],
                    'appName' => $searchParams["name"]
                        ]
        );
    }

}
