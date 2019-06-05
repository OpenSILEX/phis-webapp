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
     * Define the behaviors of the controller
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
     * Show in a gallery all available R applications
     * @return string result of a view
     */
    public function actionIndex() {
        $searchModel = new DataAnalysisAppSearch();

        $searchParams = Yii::$app->request->queryParams;

        $searchResult = $searchModel->search($searchParams);

        // no applications returned - connection error or no opencpu applications
        // are available
        if (empty($searchResult)) {
            return $this->render('/site/error', [
                        'name' => Yii::t('app/warning', 'Informations'),
                        'message' => Yii::t('app/messages', 'No application available')
                        ]
            );
        } else {
            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        ]
            );
        }
    }

    /**
     * Show standalone Demo R application integrated in a iframe.
     * The purpose of this application is to test a R function 
     * which use any OpenSILEX webservice.
     * It is a specific demo application that why it is fixed.
     * @return string a view result
     */
    public function actionViewDemo() {
        $searchModel = new DataAnalysisAppSearch();
        // retreive information on default demo application
        $appDemo = $searchModel->getApplicationInformation(
                $searchModel::DEFAULT_TEST_DEMO_APP
        );
        // connection error or application not loaded
        if (!empty($appDemo)) {
            $appDemoInformation = $appDemo[$searchModel::DEFAULT_TEST_DEMO_APP];
            $this->redirect($appDemoInformation[DataAnalysisAppSearch::APP_INDEX_URL]);
        } else {
            return $this->render('/site/error', [
                        'name' => Yii::t('app/warning', 'Informations'),
                        'message' => Yii::t('app/messages', 'Demonstration application not available')
                        ]
            );
        }
    }

    /**
     * Show standalone Demo R application integrated in a iframe.
     * @return string a view result
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