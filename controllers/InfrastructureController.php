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

    CONST ANNOTATIONS_PROVIDER = "annotationsProvider";
    CONST EVENTS_PROVIDER = "eventsProvider";

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
            $searchParams[YiiModelsConstants::PAGE]--;
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
        $infrastructureDetail = $model->getDetails(Yii::$app->session[WSConstants::ACCESS_TOKEN], $id, Yii::$app->language);

        //2. Get documents.
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documentsProvider = $searchDocumentModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], ["concernedItem" => $id]);
        
        //2. get events
        $searchEventModel = new EventSearch();
        $searchEventModel->concernedItemUri = $id;
        $eventSearchParameters = [];
        if (isset($searchParams[WSConstants::EVENT_WIDGET_PAGE])) {
            $eventSearchParameters[WSConstants::PAGE] = $searchParams[WSConstants::EVENT_WIDGET_PAGE] - 1;
        }
        $eventSearchParameters[WSConstants::PAGE_SIZE] = Yii::$app->params['eventWidgetPageSize'];
        $eventsProvider = $searchEventModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $eventSearchParameters);
        $eventsProvider->pagination->pageParam = WSConstants::EVENT_WIDGET_PAGE;

        //4. Get annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $annotationsProvider = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);

        //5. Render the view of the infrastructure.
        if (is_array($infrastructureDetail) && isset($infrastructureDetail[WSConstants::TOKEN])) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                        'model' => $infrastructureDetail,
                        'dataDocumentsProvider' => $documentsProvider,
                        self::EVENTS_PROVIDER => $eventsProvider,
                        self::ANNOTATIONS_PROVIDER => $annotationsProvider
            ]);
        }
    }
}
