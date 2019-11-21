<?php
//******************************************************************************
//                          SensorController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: Jun, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiSensorModel;
use app\models\yiiModels\SensorSearch;
use app\models\yiiModels\DeviceDataSearch;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\yiiModels\YiiVariableModel;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\YiiModelsConstants;
use app\models\yiiModels\UserSearch;
use app\models\wsModels\WSConstants;

/**
 * CRUD actions for SensorModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiSensorModel
 * @update [Morgane Vidal] 13 March, 2018: add link documents to sensors
 * @update [Arnaud Charleroy] 23 August, 2018: add annotations list linked to an instance viewed and update coding style
 * @update [Vincent Migot] 7 November, 2018: Add sensor/variables link
 * @update [Vincent Migot] 19 November, 2018: Add visualization of environmental data
 * @update [Andréas Garcia] 11 March, 2019: Add event widget
 * @update [Arnaud Charleroy] 30 October, 2019: Add sensor data by data service
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class SensorController extends Controller {
    
    const APERTURE = "aperture";
    const FOCAL_LENGTH = "focalLength";
    const LENS = "Lens";
    const PROPERTIES = "properties";
    const RELATION = "relation";
    const VALUE = "value";
    const RDF_TYPE = "rdfType";
    const URI = "uri";
    
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
     * Gets the sensors types (complete URI)
     * @return array list of the sensors types URIs 
     * @example
     *  [
     *    "http://www.opensilex.org/vocabulary/oeso#RGBImage",
     *    "http://www.opensilex.org/vocabulary/oeso#HemisphericalImage"
     *  ]
     */
    public function getSensorsTypesUris() {
        $model = new YiiSensorModel();
        
        $sensorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $sensingDevicesConcepts = $model->getSensorsTypes(Yii::$app->session['access_token']);
            if ($sensingDevicesConcepts === "token") {
                return "token";
            } else {
                $totalPages = $sensingDevicesConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];
                foreach ($sensingDevicesConcepts[WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[] = $sensorType->uri;
                }
            }
        }
        
        return $sensorsTypes;
    }
    
    /**
     * Gets the sensors types
     * @return array list of the sensors types URIs 
     * @example
     * [
     *   "RGBImage",
     *   "HemisphericalImage"
     * ]
     */
    public function getSensorsTypes() {
        $model = new YiiSensorModel();
        
        $sensorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $sensingDevicesConcepts = $model->getSensorsTypes(Yii::$app->session['access_token']);
            if ($sensingDevicesConcepts === "token") {
                return "token";
            } else {
                $totalPages = $sensingDevicesConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];

                foreach ($sensingDevicesConcepts[WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[] = explode("#", $sensorType->uri)[1];
                }
            }
        }
        return $sensorsTypes;
    }
    
    /**
     * @return array the list of the sensors types with the sensor type label and URI. 
     * @example 
     * [
     *   "http://sensor/type/uri" => "Sensor",
     *   ...
     * ]
     */
    public function getSensorsTypesSimpleAndUri() {
        $model = new YiiSensorModel();
        
        $sensorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $sensingDevicesConcepts = $model->getSensorsTypes(Yii::$app->session['access_token']);
            if ($sensingDevicesConcepts === "token") {
                return "token";
            } else {
                $totalPages = $sensingDevicesConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];

                foreach ($sensingDevicesConcepts[WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[$sensorType->uri] = explode("#", $sensorType->uri)[1];
                }
            }
        }
        return $sensorsTypes;
    }
    
    /**
     * Generates the sensor creation page
     * @return mixed
     */
    public function actionCreate() {
        $sensorModel = new YiiSensorModel();
        
        $sensorsTypes = $this->getSensorsTypes();
        if ($sensorsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $usersModel = new YiiUserModel();
        $users = $usersModel->getPersonsMailsAndName(Yii::$app->session['access_token']);
        
        return $this->render('create', [
            'model' => $sensorModel,
            'sensorsTypes' => json_encode($sensorsTypes, JSON_UNESCAPED_SLASHES),
            'users' => json_encode(array_keys($users))
        ]);
    }
    
    /**
     * @param string $sensorType
     * @return string the complete sensor type URI corresponding to the given 
     * sensor type
     * @example http://www.opensilex.org/vocabulary/oeso#RGBImage
     */
    private function getSensorTypeCompleteUri($sensorType) {
        $sensorsTypes = $this->getSensorsTypesUris();
        foreach ($sensorsTypes as $sensorTypeUri) {
            if (strpos($sensorTypeUri, $sensorType)) {
                return $sensorTypeUri;
            }
        }
        return null;
    }
    
    /**
     * Searches a sensor by its URI.
     * @param String $uri searched sensor's URI
     * @return mixed YiiSensorModel: the searched sensor
     *               "token" if the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $sensorModel = new YiiSensorModel();
        $requestRes = $sensorModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $sensorModel;
        } else if(isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * Creates the given sensors
     * @return string of the creation JSON 
     */
    public function actionCreateMultipleSensors() {
        $sensors = json_decode(Yii::$app->request->post()["sensors"]);
        $sessionToken = Yii::$app->session['access_token'];
        if (count($sensors) > 0) {
            $sensorsUris = null;
            foreach ($sensors as $sensor) {
              $forWebService = null;
              $sensorModel = new YiiSensorModel();
              $sensorModel->rdfType = $this->getSensorTypeCompleteUri($sensor[2]);
              $sensorModel->label = $sensor[1];
              $sensorModel->brand = $sensor[3];
              $sensorModel->inServiceDate = $sensor[7];
              $sensorModel->personInCharge = $sensor[9];
              
              if ($sensor[4] !== "") {
                  $sensorModel->serialNumber = $sensor[4];
              }
              if ($sensor[5] !== "") {
                  $sensorModel->model = $sensor[5];
              }
              if ($sensor[6] !== "") {
                  $sensorModel->dateOfPurchase = $sensor[6];
              }
              if ($sensor[8] !== "") {
                  $sensorModel->dateOfLastCalibration = $sensor[8];
              }
              
              $forWebService[] = $sensorModel->attributesToArray();
              $insertionResult = $sensorModel->insert($sessionToken, $forWebService);
              
              $sensorsUris[] = $insertionResult->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
            }
            return json_encode($sensorsUris, JSON_UNESCAPED_SLASHES); 
        }
        return true;
    }
    
    /**
     * Lists all sensors
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SensorSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;        
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[YiiModelsConstants::PAGE]--;
        }

        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        //list of sensors
        $sensorsTypes = $this->getSensorsTypesSimpleAndUri();
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN_INVALID) {
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
               'sensorsTypes' => $sensorsTypes
            ]);
        }
    }
    
    /**
     * Returns the profile of a sensor
     * @param string $uri
     * @return array array corresponding to a sensor profile
     */
    private function getSensorProfile($uri) {
        $sensorModel = new YiiSensorModel();
        $sensorModel->getSensorProfile(Yii::$app->session['access_token'], $uri);
        return $sensorModel->properties;
    }
    
    /**
     * Displays a single sensor model
     * @return mixed
     */
    public function actionView($id) {
        //0. Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        $res = $this->findModel($id);
        
        //get sensor profile
        $res["properties"] = $this->getSensorProfile($id);
        
        //get sensor's linked documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get sensor annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $sensorAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
        
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
     
        //5. get sensor variables
        $variableModel = new YiiVariableModel();
        $variables = $variableModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session[WSConstants::ACCESS_TOKEN]);

        if ($res === WSConstants::TOKEN_INVALID) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {            
            $dataSearchModel = new DeviceDataSearch();
            $dataSearchModel->sensorURI = $res->uri;

            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                'variables' => $variables,
                'dataSearchModel' => $dataSearchModel,
                self::ANNOTATIONS_PROVIDER => $sensorAnnotations,
                self::EVENTS_PROVIDER => $eventsProvider
            ]);
        }
    }
    
    /**
     * Ajax action to update the list of variables measured by a sensor
     * @return the web service result with success or error
     */
    public function actionUpdateVariables() {
        $post = Yii::$app->request->post();
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];        
        $sensorUri = $post["uri"];
        if (isset($post["items"])) {
            $variablesUri = $post["items"];
        } else {
            $variablesUri = [];
        }
        $sensorModel = new YiiSensorModel();
        
        $res = $sensorModel->updateVariables($sessionToken, $sensorUri, $variablesUri);
        
        return json_encode($res, JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Inserts a lens in the database and return it's URI
     * @param array $post array with the characteristics of the lens
     * @return string the lens URI inserted
     */
    private function insertLensAndGetUri($post) {
        //1. insert the basic metadata
        $lensModel = new YiiSensorModel();
        $lensModel->rdfType = Yii::$app->params[SensorController::LENS];
        $lensModel->label = $post["lensLabel"];
        $lensModel->brand = $post["lensBrand"];
        $lensModel->inServiceDate = $post["lensInServiceDate"];
        $lensModel->personInCharge = $post["lensPersonInCharge"];
        
        $forWebService[] = $lensModel->attributesToArray();
        
        $requestRes = $lensModel->insert(Yii::$app->session[WSConstants::ACCESS_TOKEN], $forWebService);
                
        $lensUri = $requestRes->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
        
        //2. inserts the lens profile
        $lensProfile[SensorController::URI] = $lensUri;
        
        $apertureProperty[SensorController::RELATION] = Yii::$app->params[SensorController::APERTURE];
        $apertureProperty[SensorController::VALUE] = $post["lensAperture"];
        
        $lensProfileProperties[] = $apertureProperty;
        
        $focalLengthProperty[SensorController::RELATION] = Yii::$app->params[SensorController::FOCAL_LENGTH];
        $focalLengthProperty[SensorController::VALUE] = $post["lensFocalLength"];
        
        $lensProfileProperties[] = $focalLengthProperty;
        
        $lensProfile[SensorController::PROPERTIES] = $lensProfileProperties;
        $lensProfiles[] = $lensProfile;
        
        $lensModel->insertProfile(Yii::$app->session[WSConstants::ACCESS_TOKEN], $lensProfiles);
                
        return $lensUri;
    }
    
    /**
     * Checks if a given string is part of the list of the lens properties 
     * provided by the form view
     * @param string $propertyLabel
     * @return boolean
     */
    private function isLensProperty($propertyLabel) {
        return $propertyLabel == "lensLabel" 
            || $propertyLabel == "lensBrand"
            || $propertyLabel == "lensInServiceDate"
            || $propertyLabel == "lensPersonInCharge"
            || $propertyLabel == "lensAperture"
            || $propertyLabel == "lensFocalLength";
    }
    
    /**
     * Extract the information corresponding to the sensor profile from a given 
     * post and returns an array with the sensor profile
     * @param array $post
     * @return array
     */
    private function getSensorProfileArrayFromPost($post) {
        $sensorProfile[SensorController::URI] = $post["YiiSensorModel"]["uri"];
        $sensorProperties = null;
        foreach($post as $key => $value) {
            if ($value !== "" && $value !== null && is_string($value)) {
                //if it is not a lens property
                if (!$this->isLensProperty($key)) {
                    $sensorProperty = null;
                    if ($key === "lensUri") {
                        $sensorProperty[SensorController::RDF_TYPE] = Yii::$app->params[SensorController::LENS];
                    }
                    
                    if (YiiSensorModel::getPropertyFromKey($key) !== null) {
                        $sensorProperty[SensorController::RELATION] = YiiSensorModel::getPropertyFromKey($key);
                        $sensorProperty[SensorController::VALUE] = $value;
                        $sensorProperties[] = $sensorProperty;
                    }
                }
            }
        }
        
        $sensorProfile[SensorController::PROPERTIES] = $sensorProperties;
        
        return $sensorProfile;
    }
    
    /**
     * 
     * @param mixed $users users list
     * @return ArrayHelper of the users email => email
     */
    private function usersToMap($users) {
        if ($users !== null) {
            return ArrayHelper::map($users, 'email', 'email');
        } else {
            return null;
        }
    }
    
    /**
     * Adds a sensor profile 
     * @return mixed
     */
    public function actionCharacterize($sensorUri) {
        $sensorModel = new YiiSensorModel();
        
        if ($sensorModel->load(Yii::$app->request->post())) {
            
            $post = Yii::$app->request->post();
            
            //1. if needed, create lens first and get it's uri
            $lensUri = null;
            if (isset(Yii::$app->request->post()["lensBrand"]) && Yii::$app->request->post()["lensBrand"] !== "") {
                $lensUri = $this->insertLensAndGetUri($post);
            }
            
            if ($lensUri !== null) {
                $post["lensUri"] = $lensUri;
            }
            
            $sensorProfileToAdd[] = $this->getSensorProfileArrayFromPost($post);
            $requestRes = $sensorModel->insertProfile(Yii::$app->session[WSConstants::ACCESS_TOKEN], $sensorProfileToAdd);
            
            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else if (is_string($requestRes)) { //server error
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $requestRes]);
            } else {          
                return $this->redirect(['view', 'id' => $requestRes[0]]);
            }
            
        } else {
            $sensor = $this->findModel($sensorUri);
            
            //get all users emails (for the person in charge if a lens needs to be created)
           $searchUsersModel = new UserSearch();
           $users = $this->usersToMap($searchUsersModel->find(Yii::$app->session[WSConstants::ACCESS_TOKEN], []));

            if (is_string($sensor) && $sensor === WSConstants::TOKEN) { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else if (is_string($sensor)) { //server error
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $sensorsTypes]);
            } else {
                return $this->render('characterize', [
                   'model' => $sensorModel,
                   'sensor' => $sensor,
                   'users' => $users
                ]);
            }
        }
    }
       
    /**
     * Ajax action which returns the HTML graph corresponding to the SensorDataSearch POST parameters
     * @return string
     */
    public function actionSearchData() {
        $searchModel = new \app\models\yiiModels\DeviceDataSearch();
        
        // Load POST parameters
        if ($searchModel->load(Yii::$app->request->post())) {
            
            // Get data
            $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
            $sensorGraphData = $searchModel->getEnvironmentData($sessionToken);
            
            // Render data
            return $this->renderAjax('_view_sensor_graph', [
                'sensorGraphData' => $sensorGraphData
            ]);
        }
    }
    
    /**
     * @param array $sensorsTypes
     * @return arra list of the sensors types in the right format
     * [
     *      "http://www.opensilex.org/vocabulary/oeso#Thermocouple" => "Thermocouple",
     *      ...
     * ]
     */
    private function sensorsTypesToMap($sensorsTypes) {
        $toReturn;
        foreach($sensorsTypes as $type) {
            $toReturn["http://www.opensilex.org/vocabulary/oeso#" . $type] = $type;
        }
        
        return $toReturn;
    }
    
    /**
     * Updates a sensor
     * @param string $id URI of the sensor to update
     * @return mixed the page to show
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiSensorModel();
        $model->uri = $id;
        
        // if the form is complete, try to update sensor
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
            
            // list of sensor's types
            $sensorsTypes = $this->getSensorsTypes();
            if ($sensorsTypes === "token") {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
        
            $usersModel = new YiiUserModel();
            $users = $usersModel->getPersonsMailsAndName(Yii::$app->session['access_token']);

            return $this->render('update', [
                'model' => $model,
                'types' => $this->sensorsTypesToMap($sensorsTypes),
                'users' => $users
            ]);
        }
    }
}
