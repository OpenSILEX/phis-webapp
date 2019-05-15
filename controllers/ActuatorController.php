<?php

//******************************************************************************
//                                       ActuatorController.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiActuatorModel;
use app\models\yiiModels\ActuatorSearch;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\yiiModels\DeviceDataSearch;
use app\models\yiiModels\YiiVariableModel;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\YiiModelsConstants;
use app\models\wsModels\WSConstants;

/**
 * CRUD actions for ActuatorModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiActuatorModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class ActuatorController extends Controller {
    
    CONST ANNOTATIONS_DATA = "actuatorAnnotations";
    CONST EVENTS_PROVIDER = "actuatorEvents";
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
     * Gets the actuators types (complete URI)
     * @return array list of the actuators types URIs 
     * @example
     *  [
     *    "http://www.opensilex.org/vocabulary/oeso#Actuator"
     *  ]
     */
    public function getActuatorsTypesUris() {
        $model = new YiiActuatorModel();
        
        $actuatorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $actuatorConcepts = $model->getActuatorsTypes(Yii::$app->session['access_token']);
            if ($actuatorConcepts === "token") {
                return "token";
            } else {
                $totalPages = $actuatorConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];
                foreach ($actuatorConcepts[WSConstants::DATA] as $actuatorType) {
                    $actuatorsTypes[] = $actuatorType->uri;
                }
            }
        }
        
        return $actuatorsTypes;
    }
    
    /**
     * Gets the actuators types
     * @return array list of the actuator types URIs 
     * @example
     * [
     *   "RGBImage",
     *   "HemisphericalImage"
     * ]
     */
    public function getActuatorsTypes() {
        $model = new YiiActuatorModel();
        
        $actuatorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $actuatorConcepts = $model->getActuatorsTypes(Yii::$app->session['access_token']);
            if ($actuatorConcepts === "token") {
                return "token";
            } else {
                $totalPages = $actuatorConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];

                foreach ($actuatorConcepts[WSConstants::DATA] as $actuatorType) {
                    $actuatorsTypes[] = explode("#", $actuatorType->uri)[1];
                }
            }
        }
        return $actuatorsTypes;
    }
    
    /**
     * @return array the list of the actuator types with the actuator type label and URI. 
     * @example 
     * [
     *   "http://actuator/type/uri" => "Actuator",
     *   ...
     * ]
     */
    public function getActuatorsTypesSimpleAndUri() {
        $model = new YiiActuatorModel();
        
        $actuatorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $actuatorsConcepts = $model->getActuatorsTypes(Yii::$app->session['access_token']);
            if ($actuatorsConcepts === "token") {
                return "token";
            } else {
                $totalPages = $actuatorsConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];

                foreach ($actuatorsConcepts[WSConstants::DATA] as $actuatorType) {
                    $actuatorsTypes[$actuatorType->uri] = explode("#", $actuatorType->uri)[1];
                }
            }
        }
        return $actuatorsTypes;
    }
    
    /**
     * Generates the actuators creation page
     * @return mixed
     */
    public function actionCreate() {
        $actuatorModel = new YiiActuatorModel();
        
        $actuatorsTypes = $this->getActuatorsTypes();
        if ($actuatorsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $usersModel = new YiiUserModel();
        $users = $usersModel->getPersonsMailsAndName(Yii::$app->session['access_token']);
        
        return $this->render('create', [
            'model' => $actuatorModel,
            'actuatorsTypes' => json_encode($actuatorsTypes, JSON_UNESCAPED_SLASHES),
            'users' => json_encode(array_keys($users))
        ]);
    }
    
    /**
     * @param string $actuatorType
     * @return string the complete actuator type URI corresponding to the given 
     * actuator type
     * @example http://www.opensilex.org/vocabulary/oeso#Actuator
     */
    private function getActuatorTypeCompleteUri($actuatorType) {
        $actuatorsTypes = $this->getActuatorsTypesUris();
        foreach ($actuatorsTypes as $actuatorTypeUri) {
            if (strpos($actuatorTypeUri, $actuatorType)) {
                return $actuatorTypeUri;
            }
        }
        return null;
    }
    
    /**
     * Searches an actuator by its URI.
     * @param String $uri searched actuator's URI
     * @return mixed YiiActuatorModel: the searched actuator
     *               "token" if the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $actuatorModel = new YiiActuatorModel();
        $requestRes = $actuatorModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $actuatorModel;
        } else if(isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * Creates the given actuators
     * @return string of the creation JSON 
     */
    public function actionCreateMultipleActuators() {
        $actuators = json_decode(Yii::$app->request->post()["actuators"]);
        $sessionToken = Yii::$app->session['access_token'];
        if (count($actuators) > 0) {
            $actuatorsUris = null;
            foreach ($actuators as $actuator) {
              $forWebService = null;
              $actuatorModel = new YiiActuatorModel();
              $actuatorModel->rdfType = $this->getActuatorTypeCompleteUri($actuator[2]);
              $actuatorModel->label = $actuator[1];
              $actuatorModel->brand = $actuator[3];
              $actuatorModel->inServiceDate = $actuator[7];
              $actuatorModel->personInCharge = $actuator[9];
              
              if ($actuator[4] !== "") {
                  $actuatorModel->serialNumber = $actuator[4];
              }
              if ($actuator[5] !== "") {
                  $actuatorModel->model = $actuator[5];
              }
              if ($actuator[6] !== "") {
                  $actuatorModel->dateOfPurchase = $actuator[6];
              }
              if ($actuator[8] !== "") {
                  $actuatorModel->dateOfLastCalibration = $actuator[8];
              }
              
              $forWebService[] = $actuatorModel->attributesToArray();
              $insertionResult = $actuatorModel->insert($sessionToken, $forWebService);
              
              $actuatorsUris[] = $insertionResult->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
            }
            return json_encode($actuatorsUris, JSON_UNESCAPED_SLASHES); 
        }
        return true;
    }
    
    /**
     * Lists all actuators
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ActuatorSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;        
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[YiiModelsConstants::PAGE]--;
        }

        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        
        //list of actuators types
        $actuatorsTypes = $this->getActuatorsTypesSimpleAndUri();
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
               'dataProvider' => $searchResult,
               'actuatorsTypes' => $actuatorsTypes
            ]);
        }
    }
    
    /**
     * Displays a single actuator model
     * @return mixed
     */
    public function actionView($id) {
        //0. Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        $res = $this->findModel($id);
        
        //get actuator's linked documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get actuator annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $annotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
        
        //4. get events
        $searchEventModel = new EventSearch();
        $searchEventModel->searchConcernedItemUri = $id;
        $eventSearchParameters = [];
        if (isset($searchParams[WSConstants::EVENT_WIDGET_PAGE])) {
            $eventSearchParameters[WSConstants::PAGE] = $searchParams[WSConstants::EVENT_WIDGET_PAGE] - 1;
        }
        $eventSearchParameters[WSConstants::PAGE_SIZE] = Yii::$app->params['eventWidgetPageSize'];
        $eventsProvider = $searchEventModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $eventSearchParameters);
        $eventsProvider->pagination->pageParam = WSConstants::EVENT_WIDGET_PAGE;
     
        //5. get actuator variables
        $variableModel = new YiiVariableModel();
        $variables = $variableModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session['access_token']);

        if ($res === WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {            
            $dataSearchModel = new DeviceDataSearch();
            $dataSearchModel->sensorURI = $res->uri;

            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                'variables' => $variables,
                'dataSearchModel' => $dataSearchModel,
                self::ANNOTATIONS_DATA => $annotations,
                self::EVENTS_PROVIDER => $eventsProvider
            ]);
        }
    }
    
    /**
     * Ajax action to update the list of variables measured by an actuator
     * @return the web service result with success or error
     */
    public function actionUpdateVariables() {
        $post = Yii::$app->request->post();
        $sessionToken = Yii::$app->session['access_token'];        
        $sensorUri = $post["uri"];
        if (isset($post["items"])) {
            $variablesUri = $post["items"];
        } else {
            $variablesUri = [];
        }
        $actuatorModel = new YiiActuatorModel();
        
        $res = $actuatorModel->updateVariables($sessionToken, $sensorUri, $variablesUri);
        
        return json_encode($res, JSON_UNESCAPED_SLASHES);
    }    
       
    /**
     * Ajax action which returns the HTML graph corresponding to the DeviceDataSearch POST parameters
     * @return string
     */
    public function actionSearchData() {
        $searchModel = new \app\models\yiiModels\DeviceDataSearch();
        
        // Load POST parameters
        if ($searchModel->load(Yii::$app->request->post())) {
            
            // Get data
            $sessionToken = Yii::$app->session['access_token'];
            $actuatorGraphData = $searchModel->getEnvironmentData($sessionToken);
            
            // Render data
            return $this->renderAjax('_view_actuator_graph', [
                'actuatorGraphData' => $actuatorGraphData
            ]);
        }
    }
    
    /**
     * @param array $actuatorsTypes
     * @return arra list of the actuators types in the right format
     * [
     *      "http://www.opensilex.org/vocabulary/oeso#Thermocouple" => "Thermocouple",
     *      ...
     * ]
     */
    private function actuatorsTypesToMap($actuatorsTypes) {
        $toReturn = [];
        foreach($actuatorsTypes as $type) {
            $toReturn["http://www.opensilex.org/vocabulary/oeso#" . $type] = $type;
        }
        
        return $toReturn;
    }
    
    /**
     * Updates an actuator
     * @param string $id URI of the actuator to update
     * @return mixed the page to show
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiActuatorModel();
        $model->uri = $id;
        
        // if the form is complete, try to update actuator
        if ($model->load(Yii::$app->request->post())) {
            
            $forWebService[] = $model->attributesToArray();
            
            $requestRes = $model->update($sessionToken, $forWebService);
            
            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $model->uri]);
            }
        } else {
            $model = $this->findModel($id);
            
            // list of actuator's types
            $actuatorsTypes = $this->getActuatorsTypes();
            if ($actuatorsTypes === "token") {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
        
            $usersModel = new YiiUserModel();
            $users = $usersModel->getPersonsMailsAndName(Yii::$app->session['access_token']);
            return $this->render('update', [
                'model' => $model,
                'types' => $this->actuatorsTypesToMap($actuatorsTypes),
                'users' => $users
            ]);
        }
    }
}
