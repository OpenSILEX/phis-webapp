<?php

//******************************************************************************
//                                       InfrastructureController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 Aug, 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiInfrastructureModel;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;

/**
 * CRUD actions for YiiInfrastructureModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiInfrastructureModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class InfrastructureController extends Controller {
    
     CONST ANNOTATIONS_DATA = "infrasctructureAnnotations";
    
    /**
     * Define the behaviors
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
     * Render the view page of the infrascture corresponding to the given uri.
     * @param string $id The uri of the infrastructure
     * @return mixed
     */
    public function actionView($id) {
        //SILEX:warning
        //In this first version of infrastructure, there is only one infrastructure and it's data is in the code. 
        //There is no call to the infrastructure service
        //\SILEW:warning

        //1. Fill the infrastructure model with the information.
        $infrastructure = new YiiInfrastructureModel();
        $infrastructure->uri = $id;
        $infrastructure->alias = Yii::$app->params['platform'];
        $infrastructure->rdfType = "oepo:Installation";

        //2. Get documents.
        $searchDocumentsModel = new \app\models\yiiModels\DocumentSearch();
        $searchDocumentsModel->concernedItem = $id;
        $documentsSearch = $searchDocumentsModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);

        //3. get project annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $infrastructureAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
     
        //4. Render the view of the infrastructure.
        if (is_string($documentsSearch)) {
            if ($documentsSearch === "token") { //User must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else { //An error occurred
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $documentsSearch]);
            }
        } else {
            $infrastructure->documents = $documentsSearch;
            return $this->render('view', [
               'model' => $infrastructure,
                self::ANNOTATIONS_DATA => $infrastructureAnnotations
            ]);
        }
    } 
}
