<?php

//******************************************************************************
//                            ImageController.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 jan. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\models\yiiModels\DataFileSearch;

include_once '../config/web_services.php';

/**
 * CRUD actions for YiiImageModel
 * @update [Andréas Garcia] 15 Jan., 2019: change "concern" occurences to "concernedItem"
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
     * search images (by concerned items, rdf type) and return the result
     * @return the images corresponding to the search
     * @throws Exception
     */
    public function actionSearchFromLayer() {
        $searchModel = new DataFileSearch($pageSize = 100);
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

    /**
     * search images (for one concerned item. with rdf type, date and json filter value ) 
     * and return the result
     * @return the images corresponding to the search
     * @throws Exception
     */
    public function actionSearchFromScientificObject() {
        $searchModel = new DataFileSearch($pageSize = 100);
        if ($searchModel->load(Yii::$app->request->post())) {
            $searchModel->startDate = Yii::$app->request->post()["startDate"];
            $searchModel->endDate = Yii::$app->request->post()["endDate"];
            $searchModel->concernedItems = Yii::$app->request->post()["concernedItems"];
            $searchModel->jsonValueFilter = Yii::$app->request->post()["jsonValueFilter"];
            $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->post());

            return $this->renderAjax('_simple_images_visualization', [
                        'model' => $searchModel,
                        'data' => $searchResult
            ]);
        }
        return $this->renderAjax('_simple_images_visualization', [
                    'model' => $searchModel
        ]);
    }

    /**
     * Proxy action to get data file image from web service
     * @param type $imageUri
     */
    public function actionGet($imageUri) {
        $url = WS_PHIS_PATH . "data/file/" . $imageUri;
        $imginfo = getimagesize($url);
        header("Content-type: " . $imginfo['mime']);
        header('Content-Transfer-Encoding: binary');
        $file = fopen($url, 'rb');

        ob_end_clean();
        fpassthru($file);
        exit;
    }

}
