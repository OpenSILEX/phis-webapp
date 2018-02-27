<?php

//******************************************************************************
//                                       ImageController.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: implements the CRUD actions for YiiImageModel
//******************************************************************************

namespace app\controllers;
use Yii;
use yii\filters\VerbFilter;

/**
 * CRUD actions for YiiImageModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiImageModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
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
     * search images (by concerned element, rdf type) and return the result
     * @return the images corresponding to the search
     * @throws Exception
     */
    public function actionSearchFromLayer() {
        $searchModel = new \app\models\yiiModels\ImageSearch($pageSize = 100);
        if ($searchModel->load(Yii::$app->request->post())) {
            $searchModel->concern = Yii::$app->request->post()["concernedElements"];
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
