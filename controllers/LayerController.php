<?php

//**********************************************************************************************
//                                       LayerController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 29 2017
// Subject: implements the CRUD for the layers (WSLayerModel)
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\yiiModels\YiiLayerModel;

/**
 * CRUD actions for YiiLayerModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiLayerModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class LayerController extends Controller {
    
    /**
     * define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
          'verbs' => [
              'class' => \yii\filters\VerbFilter::className(),
              'actions' => [
                  'delete' => ['POST']
              ]
          ]  
        ];
    }
    
    /**
     * Generate a layer
     * @param string $objectURI the element to visualize
     * @param string $objectType the type of the element to visualize
     * @param string $depth true if we wants to see all descendants,
     *                     false if we only wanna get the direct children
     * @return string|YiiLayerModel "token" if the user must log in,
     *                              YiiLayerModel with the filePath
     */
    private function postLayer($objectURI, $objectType, $depth, $generateFile) {
        $layerModel = new YiiLayerModel();
        $layerModel->objectURI = $objectURI;
        $layerModel->objectType = $objectType;
        $layerModel->depth = $depth;
        $layerModel->generateFile = $generateFile;
        
        $sessionToken = Yii::$app->session['access_token'];
        $dataToSend[] = $layerModel->attributesToArray();
        
        $requestRes = $layerModel->insert($sessionToken, $dataToSend);
        
        if (is_string($requestRes) && $requestRes === "token") { //user must connect
            return "token";
        } else {
            $layerModel->filePath = isset($requestRes->metadata->datafiles) ? $requestRes->metadata->datafiles[0] : null;
            return $layerModel;
        }
    }
    
    /**
     * Displays the layer corresponding to the objectURI
     * @param string $objectURI the element to visualize
     * @param string objectType the type of the element to visualize
     * @param string depth true if we wants to see all descendants,
     *                     false if we only wanna get the direct children
     * @return mixed
     */
    public function actionView($objectURI, $objectType, $depth, $generateFile) {
        //SILEX:todo
        //needs to calls the web service to get the geojson link and return it 
        //to the PHP view.
        ///!\during the redirection to the action view, think to send 
        //the 3 params to call the ws
        //\SILEX:todo
        $layerModel = $this->postLayer($objectURI, $objectType, $depth, $generateFile);
        if (is_string($layerModel) && $layerModel === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else if (is_string($layerModel)) {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $layerModel]);
        } else {
            return $this->render('view', [
                        'model' => $layerModel
                    ]);
        }
    }
}