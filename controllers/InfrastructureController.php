<?php

//******************************************************************************
//                           InfrastructureController.php
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
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\yiiModels\YiiModelsConstants;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\DocumentSearch;
use app\components\helpers\SiteMessages;

/**
 * CRUD actions for YiiInfrastructureModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiInfrastructureModel
 * @update [Vincent Migot] 20 Sept, 2018: Implement infrastructures service and detail view
 * @update [Andréas Garcia] 11 March, 2019: Add event widget
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class InfrastructureController extends Controller {

    CONST ANNOTATIONS_DATA = "infrastructureAnnotations";
    CONST EVENTS_DATA = "infrastructureEvents";

    /**
     * Defines the behaviors
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
     * Lists all infrastructures
     * @return mixed
     */
    public function actionIndex() {
        $infrastructuresModel = new InfrastructureSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE]--;
        }
        
        $searchResult = $infrastructuresModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);

        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN_INVALID) {
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
     * Renders the view page of the infrastructure corresponding to the given URI.
     * @param string $id The URI of the infrastructure
     * @return mixed
     */
    public function actionView($id) {
        //0. Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        
        //1. Fill the infrastructure model with the information.
        $model = new YiiInfrastructureModel();
        $infrastructureDetail = $model->getDetails(Yii::$app->session['access_token'], $id, Yii::$app->language);

        //2. Get documents.
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. Get events
        $searchEventModel = new EventSearch();
        $searchEventModel->concernedItemUri = $id;
        $searchEventModel->pageSize = Yii::$app->params['eventWidgetPageSize'];
        $events = $searchEventModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);

        //4. Get annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $infrastructureAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);

        //5. Render the view of the infrastructure.
        if (is_array($infrastructureDetail) && isset($infrastructureDetail["token"])) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                        'model' => $infrastructureDetail,
                        'dataDocumentsProvider' => $documents,
                        self::EVENTS_DATA => $events,
                        self::ANNOTATIONS_DATA => $infrastructureAnnotations
            ]);
        }
    }
}
