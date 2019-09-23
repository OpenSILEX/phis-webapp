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

    
    
    public function actionGallery() {
        $dataProvider =[
            'spatial' =>
                [
                    'label' => 'Spatial vizualisation',
                    'items' => [
                        'mapField' => [
                                'descriptionFilePath' => 'mapField/mapField.html', 
                                'RfunctionPath' => 'mapField/mapField.R',
                                    ]
                        ],
                ],
            'timeSeries' =>
                [
                    'label' => 'Time series',
                    'items' => [
                        'plotVar' => [
                                'descriptionFilePath' => 'plotVar/vignette.png', 
                                'RfunctionPath' => 'plotVar/plotVar.R',
                                    ]
                        ]
                ],
            'lineGraphs' =>
                [
                    'label' => 'Line graphs',
                    'items' => [
                        'compareVarieties' => [
                                'descriptionFilePath' => 'compareVarieties/vignette.png', 
                                'RfunctionPath' => 'compareVarieties/compareVarieties.R',
                                    ]
                        ]
                ]
        ];
        
        
        return $this->render('gallery', [
                    'galleryFilePath' => '@app/web/RGallery',
                    'dataProvider' => $dataProvider
                    ]
        );
    }
    
    public function actionViewGalleryItem() {
        $searchParams = Yii::$app->request->queryParams;
        return $this->render('view-gallery-item', [
                    'descriptionFilePath' => $searchParams["descriptionFilePath"],
                    'RfunctionPath' => $searchParams["RfunctionPath"]
                    ]
        );
    }
}