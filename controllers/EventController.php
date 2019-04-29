<?php
//******************************************************************************
//                          EventController.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: Jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use app\controllers\GenericController;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventCreation;
use app\models\yiiModels\EventUpdate;
use app\models\yiiModels\InfrastructureSearch;
use app\models\wsModels\WSConstants;
use app\components\helpers\SiteMessages;

/**
 * Controller for the events.
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiEventModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventController extends GenericController {    
    const PARAM_ANNOTATIONS_DATA_PROVIDER = "paramAnnotations";
    const PARAM_UPDATABLE = "paramUpdatable";
    
    const ANNOTATIONS_PAGE = "annotations-page";
    const INFRASTRUCTURES_DATA = "infrastructures";
    const INFRASTRUCTURES_DATA_URI = "infrastructureUri";
    const INFRASTRUCTURES_DATA_LABEL = "infrastructureLabel";
    const INFRASTRUCTURES_DATA_TYPE = "infrastructureType";
    const EVENT_TYPES = "eventTypes";
    
    const PARAM_CONCERNED_ITEMS_URIS = 'concernedItemsUris';
    const PARAM_RETURN_URL = "returnUrl";

    /**
     * The return URL after annotation creation
     * @var string 
     */
    public $returnUrl;
    
    /**
     * Lists the events.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EventSearch();
        
        $searchParams = Yii::$app->request->queryParams;
        
        if (isset($searchParams[WSConstants::PAGE])) {
            $searchParams[WSConstants::PAGE] = $searchParams[WSConstants::PAGE] - 1;
        }
        $searchParams[WSConstants::PAGE_SIZE] = Yii::$app->params['indexPageSize'];
        
        $searchResult = $searchModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN_INVALID) {
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
        // Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        
        // Fill the event model with the information
        $event = (new YiiEventModel())->getEvent(Yii::$app->session[WSConstants::ACCESS_TOKEN], $id);

        // Get documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documentProvider = $searchDocumentModel->search(
                Yii::$app->session[WSConstants::ACCESS_TOKEN], 
                [YiiEventModel::CONCERNED_ITEMS => $id]);
        
        // Get annotations
        $annotationProvider = $event->getEventAnnotations(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        $annotationProvider->pagination->pageParam = self::ANNOTATIONS_PAGE;

        // Render the view of the event
        if (is_array($event) && isset($event[WSConstants::TOKEN_INVALID])) {
            return redirectToLoginPage();
        } else {
            return $this->render('view', [
                'model' =>  $event,
                'dataDocumentsProvider' => $documentProvider,
                self::PARAM_ANNOTATIONS_DATA_PROVIDER => $annotationProvider,
                self::PARAM_UPDATABLE => !$this->hasUnupdatableProperties($event)   
            ]);
        }
    }
    
    private function hasUnupdatableProperties($eventAction) : bool {
        foreach($eventAction->properties as $property) {
            if($property->relation !== Yii::$app->params['from']
                    && $property->relation !== Yii::$app->params['to']) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Gets the event types URIs.
     * @return event types URIs 
     */
    public function getEventsTypes() {
        $model = new YiiEventModel();
        
        $eventsTypes = [];
        $model->page = 0;
        $model->pageSize = Yii::$app->params['webServicePageSizeMax'];
        $eventsTypesConcepts = $model->getEventsTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        if ($eventsTypesConcepts === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($eventsTypesConcepts[WSConstants::DATA] as $eventType) {
                $eventsTypes[$eventType->uri] = $eventType->uri;
            }
        }
        
        return $eventsTypes;
    }
    
    /**
     * Gets all infrastructures.
     * @return experiments 
     */
    public function getInfrastructuresUrisTypesLabels() {
        $model = new InfrastructureSearch();
        $model->page = 0;
        $infrastructuresUrisTypesLabels = [];
        $infrastructures = $model->search(Yii::$app->session['access_token'], null);
        if ($infrastructures === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
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
     * Displays the form to create an event or creates it in case of form submission.
     * @return mixed redirect in case of error or after successfully create 
     * the event otherwise return the "create" view 
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $event = new EventCreation();
        $event->isNewRecord = true;
        
        if (!$event->load(Yii::$app->request->post())) { //display form
            $event->load(Yii::$app->request->get(), '');
            $event->creator = $this->getCreatorUri($sessionToken);
            $this->loadFormParams();
            return $this->render('create', ['model' =>  $event]);
            
        } else { // submit form         
            $dataToSend[] = $event->attributesToArray(); 
            $requestResults =  $event->insert($sessionToken, $dataToSend);
            return $this->handlePostPutResponse($requestResults, $event->returnUrl);
        }
    }

    /**
     * Displays the form to update an event.
     * @return mixed redirect in case of error or after successfully updating 
     * the event otherwise returns the "update" view 
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $event = new EventUpdate();
        $event->isNewRecord = false;
        
        if (!$event->load(Yii::$app->request->post())) {
            $event = $event->getEvent($sessionToken, $id);
            $this->loadFormParams();
            return $this->render('update', ['model' =>  $event]);
        } else {
            $dataToSend[] = $event->attributesToArray(); 
            $requestResults =  $event->update($sessionToken, $dataToSend);
            return $this->handlePostPutResponse($requestResults, ['view', 'id' =>  $event->uri]);
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
     * Gets the creator of an event.
     */
    private function getCreatorUri($sessionToken) {
        $userModel = new YiiUserModel();
        $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
        return $userModel->uri;
    }
}
