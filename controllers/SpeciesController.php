<?php

//******************************************************************************
//                                       SpeciesController.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 déc. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;

use app\models\yiiModels\SpeciesSearch;

/**
 * Implements the controller for the Species and according to YiiSpeciesModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiSpeciesModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class SpeciesController extends \yii\base\Controller {
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
     * List all Species
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SpeciesSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;
        $searchModel->language = Yii::$app->language;
        if (isset($searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE]--;
        }
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
               'dataProvider' => $searchResult
            ]);
        }
    }
}