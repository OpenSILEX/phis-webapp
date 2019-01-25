<?php

//******************************************************************************
//                            ImageController.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 jan. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date: 3 jan. 2018
// Subject: implements the CRUD actions for YiiImageModel
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\models\yiiModels\ImageSearch;

/**
 * CRUD actions for YiiImageModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiImageModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @update [Andréas Garcia] <andreas.garcia@inra.fr> 15 Jan., 2019: change 
 * "concern" occurences to "concernedItem"
 */
class ImageController extends \yii\web\Controller {
    
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
     * search images (by concerned items, rdf type) and return the result
     * @return the images corresponding to the search
     * @throws Exception
     */
    public function actionSearchFromLayer() {
        $searchModel = new ImageSearch($pageSize = 100);
        if ($searchModel->load(Yii::$app->request->post())) {
            $searchModel->concernedItems = Yii::$app->request->post()["concernedItems"];
            $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->post());
            
            return $this->renderAjax('_form_images_visualization', [ 
                        'model' => $searchModel,
                        'data' => $searchResult
                   ]);
        } else {
            return $this->renderAjax('_form_images_visualization', [
                        'model' => $searchModel
                   ]);
        }
    }
}
