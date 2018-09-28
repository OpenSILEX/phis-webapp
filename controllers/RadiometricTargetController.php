<?php

//******************************************************************************
//                          RadiometricTargetController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 27 Sept, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\yiiModels\YiiSensorModel;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;

class RadiometricTargetController extends Controller {

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
     * list all sensors
     * @return mixed
     */
    public function actionIndex() {
//        $searchModel = new \app\models\yiiModels\SensorSearch();
//        
//        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);
//        
//        if (is_string($searchResult)) {
//            return $this->render('/site/error', [
//                    'name' => Yii::t('app/messages','Internal error'),
//                    'message' => $searchResult]);
//        } else if (is_array($searchResult) && isset($searchResult["token"])) { //user must log in
//            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
//        } else {
        return $this->render('index', [
//               'searchModel' => $searchModel,
//                'dataProvider' => $searchResult
        ]);
//        }
    }

    public function actionCreate() {
        return $this->render('create', [
        ]);
    }

}
