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
use app\models\yiiModels\EventPost;
use app\models\yiiModels\YiiModelsConstants;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\YiiExperimentModel;
use app\models\yiiModels\YiiPropertyModel;
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
     * List the events
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EventSearch();
        
        $searchParams = Yii::$app->request->queryParams;
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
     * @param $id URI of the event
     * @return mixed redirect in case of error otherwise return the "view" view
     */
    public function actionView($id) {
        // Fill the event model with the information
        $event = new YiiEventModel();
        $eventDetailed = $event->getEventDetailed(Yii::$app->session['access_token'], $id);

        // Get documents
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
    
    /**
     * Get the event types URIs
     * @return event types URIs 
     */
    public function getEventsTypes() {
        $model = new YiiEventModel();
        
        $eventsTypes = [];
        $model->page = 0;
        $model->pageSize = 1000;
        $eventsTypesConcepts = $model->getEventsTypes(Yii::$app->session['access_token']);
        if ($eventsTypesConcepts === "token") {
            return "token";
        } else {
            foreach ($eventsTypesConcepts[WSConstants::DATA] as $eventType) {
                $eventsTypes[] = $eventType->uri;
            }
        }
        
        return $eventsTypes;
    }
    
    /**
     * Get the experiments
     * @return experiments 
     */
    public function getExperiments() {
        $model = new YiiExperimentModel();
        
        $experimentsUris = [];
        $model->page = 0;
        $experiments = $model->getExperimentsList(Yii::$app->session['access_token']);
        error_log("lol");
        error_log("expéo ".print_r($experiments, true));
        if ($experiments === "token") {
            return "token";
        } else {
            foreach ($experiments as $experiment) {
                $experimentsUris[$experiment->uri] = $experiment->alias;
            }
        }
        
        return $experimentsUris;
    }
    
    /**
     * Display the form to create an event or create it in case of form submission
     * @return mixed redirect in case of error or after successfully create 
     * the event otherwise return the "create" view 
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];

        $eventModel = new EventPost();
        $eventModel->load(Yii::$app->request->get(), '');
        $eventModel->isNewRecord = true;
        
        if ($eventModel->load(Yii::$app->request->post())) {
            // Set date
            $eventModel->dateWithoutTimezone = str_replace(" ", "T", $eventModel->dateWithoutTimezone);
            
            // Set model creator 
            $userModel = new YiiUserModel();
            $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
            $eventModel->creator = $userModel->uri;
            $eventModel->isNewRecord = true;
            
            // Set properties
            $property = new YiiPropertyModel();
            switch ($eventModel->rdfType) {
                case "http://www.opensilex.org/vocabulary/oeev#MoveFrom":
                    $property->value = $eventModel->propertyFrom;
                    $property->rdfType = "http://www.opensilex.org/vocabulary/oeso#Experiment";
                    $property->relation = "http://www.opensilex.org/vocabulary/oeev#from";
                    break;
                case "http://www.opensilex.org/vocabulary/oeev#MoveTo":
                    $property->value = $eventModel->propertyTo;
                    $property->rdfType = "http://www.opensilex.org/vocabulary/oeso#Experiment";
                    $property->relation = "http://www.opensilex.org/vocabulary/oeev#to";
                    break;
                default : 
                    $property = null;
                    break;
            }
            $eventModel->properties = [$property];
            
            // If post data, insert the submitted form
            $dataToSend[] =  $eventModel->attributesToArray();
            error_log("dataToSend ".print_r($dataToSend, true));
            $requestRes =  $eventModel->insert($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === "token") {
                return $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
            } else {
                if (isset($requestRes->{'metadata'}->{'datafiles'}[0])) { //project created
                    return $this->redirect(['view', 'id' => $requestRes->{'metadata'}->{'datafiles'}[0]]);
                } else { //an error occurred
                    return $this->render(SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $requestRes->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}]);
                }
            }
        } else {
            // If no post data display the create form
            $types = [];
            foreach($this->getEventsTypes() as $type) {
                $types[$type] = $type;
            }
            $this->view->params["eventPossibleTypes"] = $types;
            
            $this->view->params["experiments"] = $this->getExperiments();

            return $this->render('create', [
                'model' =>  $eventModel
            ]);
        }
    }
}
