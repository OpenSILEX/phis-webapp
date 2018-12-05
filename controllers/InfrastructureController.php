<?php

//******************************************************************************
//                                       InfrastructureController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 Aug, 2018
// Contact: morgane.vidal@inra.fr, vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\yiiModels\YiiInfrastructureModel;
use app\models\yiiModels\InfrastructureSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\DocumentSearch;
use app\components\helpers\SiteMessages;

/**
 * CRUD actions for YiiInfrastructureModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiInfrastructureModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @update [Vincent Migot] 20 Sept, 2018 : Implement infrastructures service and detail view
 */
class InfrastructureController extends Controller {

    CONST ANNOTATIONS_DATA = "infrastructureAnnotations";

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
     * List all infrastructures
     * @return mixed
     */
    public function actionIndex() {
        $infrastructuresModel = new InfrastructureSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;
        if (isset($searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE]--;
        }
        
        $searchResult = $infrastructuresModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);

        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render(SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                            SiteMessages::SITE_PAGE_NAME => SiteMessages::INTERNAL_ERROR,
                            SiteMessages::SITE_PAGE_MESSAGE => $searchResult]);
            }
        } else {
            return $this->render('index', [
                        'searchModel' => $infrastructuresModel,
                        'dataProvider' => $searchResult
            ]);
        }
    }

    /**
     * Render the view page of the infrascture corresponding to the given uri.
     * @param string $id The uri of the infrastructure
     * @return mixed
     */
    public function actionView($id) {
        //1. Fill the infrastructure model with the information.
        $model = new YiiInfrastructureModel();
        $infrastructureDetail = $model->getDetails(Yii::$app->session['access_token'], $id, Yii::$app->language);

        //2. Get documents.
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItem = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);

        //3. get project annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $infrastructureAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);

        //4. Render the view of the infrastructure.
        if (is_array($infrastructureDetail) && isset($infrastructureDetail["token"])) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                        'model' => $infrastructureDetail,
                        'dataDocumentsProvider' => $documents,
                        self::ANNOTATIONS_DATA => $infrastructureAnnotations
            ]);
        }
    }

}
