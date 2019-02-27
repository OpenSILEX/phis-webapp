<?php
//******************************************************************************
//                          EventController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: Jan, 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\YiiModelsConstants;
use app\models\wsModels\WSConstants;
use app\components\helpers\SiteMessages;

/**
 * Controller for the Events according to YiiEventModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiEventModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventController extends Controller {
    CONST ANNOTATIONS_DATA = "annotations";
    
    /**
     * list the events
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EventSearch();
        
        $searchParams = Yii::$app->request->queryParams;
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[YiiModelsConstants::PAGE]--;
        }

        $searchResult = $searchModel->searchEvents(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
            } else {
                return $this->render(SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                            SiteMessages::SITE_PAGE_NAME => SiteMessages::INTERNAL_ERROR,
                            SiteMessages::SITE_PAGE_MESSAGE => $searchResult]);
            }
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel, 
                'dataProvider' => $searchResult]);
        }
    }

    /**
     * Display the detail of an event
     * 
     * @param $id Uri of the event
     * @return mixed redirect in case of error otherwise return the "view" view
     */
    public function actionView($id) {
        // Fill the event model with the information.
        $event = new YiiEventModel();
        $eventDetailed = $event->getEventDetailed(Yii::$app->session['access_token'], $id);

        // Get documents.
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);

        // Render the view of the event
        if (is_array( $eventDetailed) && isset( $eventDetailed["token"])) {
            return $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
        } else {
            return $this->render('view', [
                'model' =>  $eventDetailed,
                'dataDocumentsProvider' => $documents,
                self::ANNOTATIONS_DATA => new ArrayDataProvider([
                    'models' => $event->annotations,
                    'totalCount' => count($event->annotations)                 
                ])
            ]);
        }
    }
}
