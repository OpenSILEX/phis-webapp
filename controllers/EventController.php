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
use yii\web\GenericController;
use yii\data\ArrayDataProvider;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventPost;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\InfrastructureSearch;
use app\models\yiiModels\YiiPropertyModel;
use app\models\wsModels\WSConstants;
use app\components\helpers\SiteMessages;

/**
 * Controller for the Events according to YiiEventModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiEventModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventController extends GenericController {
    CONST ANNOTATIONS_DATA = "annotations";
    CONST INFRASTRUCTURES_DATA = "infrastructures";
    CONST INFRASTRUCTURES_DATA_URI = "infrastructureUri";
    CONST INFRASTRUCTURES_DATA_LABEL = "infrastructureLabel";
    CONST INFRASTRUCTURES_DATA_TYPE = "infrastructureType";
    CONST EVENT_TYPES = "eventTypes";
    
    /**
     * Lists the events
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EventSearch();
        
        $searchParams = Yii::$app->request->queryParams;
        $searchResult = $searchModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        
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
     * Displays the detail of an event.
     * @param $id URI of the event
     * @return mixed redirect in case of error otherwise return the "view" view
     */
    public function actionView($id) {
        // Fill the event model with the information
        $event = new YiiEventModel();
        $eventDetailed = $event->getEventDetailed(Yii::$app->session[WSConstants::ACCESS_TOKEN], $id);

        // Get documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], ["concernedItem" => $id]);

        // Render the view of the event
        if (is_array($event) && isset($event[WSConstants::TOKEN_INVALID])) {
            return redirectToLoginPage();
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
        $eventsTypesConcepts = $model->getEventsTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        if ($eventsTypesConcepts === WSConstants::TOKEN) {
            return WSConstants::TOKEN;
        } else {
            foreach ($eventsTypesConcepts[WSConstants::DATA] as $eventType) {
                $eventsTypes[$eventType->uri] = $eventType->uri;
            }
        }
        
        return $eventsTypes;
    }
    
    /**
     * Get all infrastructures
     * @return experiments 
     */
    public function getInfrastructuresUrisTypesLabels() {
        $model = new InfrastructureSearch();
        $model->page = 0;
        $infrastructuresUrisTypesLabels = [];
        $infrastructures = $model->search(Yii::$app->session['access_token'], null);
        if ($infrastructures === WSConstants::TOKEN) {
            return WSConstants::TOKEN;
        } else {
            foreach ($infrastructures->models as $infrastructure) {
                $infrastructuresUrisTypesLabels[] =
                    [
                        self::INFRASTRUCTURES_DATA_URI => $infrastructure->uri,
                        self::INFRASTRUCTURES_DATA_LABEL => $infrastructure->label,
                        self::INFRASTRUCTURES_DATA_TYPE => $infrastructure->rdfType
                    ];
            }
        }
        
        return $infrastructuresUrisTypesLabels;
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
            $eventModel->isNewRecord = true;
            
            $this->completeEventAttributes($eventModel);
            $this->completeEventAnnotationAttributes($eventModel, $sessionToken);
            
            // If post data, insert the submitted form
            $dataToSend[] =  $eventModel->attributesToArray();
            $requestResults =  $eventModel->insert($sessionToken, $dataToSend);
            return $this->handlePostResponse($requestResults, $eventModel->returnUrl);
        } else {
            $this->loadFormParams();
            return $this->render('create', ['model' =>  $eventModel]);
        }
    }

    /**
     * Displays the form to update an event.
     * @return mixed redirect in case of error or after successfully create 
     * the radiometric target otherwise return the "create" view 
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];

        $event = new EventPut();
        
        if ( $event->load(Yii::$app->request->put())) {

            if (is_string($requestRes) && $requestRes === WSConstants::TOKEN) {
            return $this->redirectToLoginPage();
            } else {
                return $this->redirect(['view', 'id' =>  $event->uri]);
            }
        } else {
            $event =  $event->getEvent($sessionToken, $id);
            $this->loadFormParams();
            return $this->render('update', ['model' =>  $event]);
        }
    }
    
    /**
     * Loads params used by the forms (creation or update).
     */
    private function loadFormParams() {
        $this->view->params[self::EVENT_TYPES] = $this->getEventsTypes();
        $this->view->params[self::INFRASTRUCTURES_DATA] = $this->getInfrastructuresUrisTypesLabels();
    }
    
    /**
     * Gets a property object according to the data entered in the creation form.
     * @param type $eventModel
     */
    private function getPropertyInCreation($eventModel) {
        $property = new YiiPropertyModel();
        switch ($eventModel->rdfType) {
            case $eventConceptUri = Yii::$app->params['moveFrom']:
                $property->value = $eventModel->propertyFrom;
                $property->rdfType = $eventModel->propertyType;
                $property->relation = Yii::$app->params['from'];
                break;
            case $eventConceptUri = Yii::$app->params['moveTo']:
                $property->value = $eventModel->propertyTo;
                $property->rdfType = $eventModel->propertyType;
                $property->relation = Yii::$app->params['to'];
                break;
            default : 
                $property = null;
                break;
        }
    }
    
    /**
     * Gets the creator of an event.
     */
    private function getCreatorUri($sessionToken) {
        $userModel = new YiiUserModel();
        $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
        return $userModel->uri;
    }
    
    /**
     * Completes the event attributes;
     */
    private function completeEventAttributes($eventModel) {
        $eventModel->dateWithoutTimezone = str_replace(" ", "T", $eventModel->dateWithoutTimezone);
        $eventModel->properties = [$this->getPropertyInCreation($eventModel)];
    }
    
    /**
     * Completes the event annotation attributes;
     */
    private function completeEventAnnotationAttributes($eventModel, $sessionToken) {
        $eventModel->creator = $this->getCreatorUri($sessionToken);
    }
}
