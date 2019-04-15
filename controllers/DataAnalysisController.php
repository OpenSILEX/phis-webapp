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
     * Show in a gallery all available R applications
     * @param boolean $integrated a parameter which 
     *        will be used to know if an application can
     *        be intergated
     * @return string result of a view
     */
    public function actionIndex($integrated = false) {

        $searchModel = new DataAnalysisAppSearch();

        $searchParams = Yii::$app->request->queryParams;

        $searchResult = $searchModel->search($searchParams);
        
        // no applications returned - connection error or no opencpu applications
        // have been loaded
        if (empty($searchResult)) {
             return $this->render('/site/error', [
                           'name' => Yii::t('app/messages','Internal error'),
                           'message' => Yii::t('app/messages', 'No application available.')
                        ]
                    );
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
     * Show standalone Demo R application app integrated in a iframe.
     * The purpose of this application is to test a R function 
     * which use any OpenSILEX webservice. 
     * @return string a view result
     */
    public function actionViewDemo() {
        $searchModel = new DataAnalysisAppSearch();
        // retreive information on default app
        $appDemo = $searchModel->getApplicationInformation(
                $searchModel::DEFAULT_TEST_DEMO_APP
                );
        // connection error or application not loaded
        if (!empty($appDemo)) {
            $appDemoInformation = $appDemo[$searchModel::DEFAULT_TEST_DEMO_APP];
            $this->redirect($appDemoInformation[DataAnalysisAppSearch::APP_INDEX_URL]);
        } else {
            return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => Yii::t('app/messages', 'Demo application not found.')]);
        }
    }

    /**
     * Show standalone Demo R app integrated in a iframe.
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
