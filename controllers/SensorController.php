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
use app\models\yiiModels\YiiSensorModel;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\VariableSearch;

/**
 * CRUD actions for SensorModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiSensorModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @update [Morgane Vidal] 13 March, 2018 : add link documents to sensors
 * @update [Arnaud Charleroy] 23 August, 2018 : add annotations list linked to an instance viewed and update coding style
 * @update [Vincent Migot] 7 November, 2018 : Add sensor/variables link
 * @update [Vincent Migot] 19 November, 2018 : Add visualization of environmental data
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
    
    CONST ANNOTATIONS_DATA = "sensorAnnotations";
    /**
     * define the behaviors
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
     * get the sensors types (complete uri)
     * @return array list of the sensors types uris 
     * e.g. [
     *          "http://www.phenome-fppn.fr/vocabulary/2017#RGBImage",
     *          "http://www.phenome-fppn.fr/vocabulary/2017#HemisphericalImage"
     *      ]
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
                $totalPages = $sensingDevicesConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];
                foreach ($sensingDevicesConcepts[\app\models\wsModels\WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[] = $sensorType->uri;
                }
            }
        }
        
        return $sensorsTypes;
    }
    
    /**
     * get the sensors types
     * @return array list of the sensors types uris 
     * e.g. [
     *          "RGBImage",
     *          "HemisphericalImage"
     *      ]
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
                $totalPages = $sensingDevicesConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];

                foreach ($sensingDevicesConcepts[\app\models\wsModels\WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[] = explode("#", $sensorType->uri)[1];
                }
            }
        }
        return $sensorsTypes;
    }
    
    /**
     * 
     * @return array the list of the sensors types with the sensor type label and the uri. 
     * e.g. 
     * [
     *  "http://sensor/type/uri" => "Sensor",
     *  ...
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
                $totalPages = $sensingDevicesConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];

                foreach ($sensingDevicesConcepts[\app\models\wsModels\WSConstants::DATA] as $sensorType) {
                    $sensorsTypes[$sensorType->uri] = explode("#", $sensorType->uri)[1];
                }
            }
        }
        return $sensorsTypes;
    }
    
    /**
     * generated the sensor creation page
     * @return mixed
     */
    public function actionCreate() {
        $sensorModel = new YiiSensorModel();
        
        $sensorsTypes = $this->getSensorsTypes();
        if ($sensorsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $usersModel = new \app\models\yiiModels\YiiUserModel();
        $usersMails = $usersModel->getUsersMails(Yii::$app->session['access_token']);
        
        return $this->render('create', [
            'model' => $sensorModel,
            'sensorsTypes' => json_encode($sensorsTypes, JSON_UNESCAPED_SLASHES),
            'users' => json_encode($usersMails)
        ]);
    }
    
    /**
     * 
     * @param string $sensorType
     * @return string the complete sensor type uri corresponding to the given 
     *                sensor type
     *                e.g. http://www.phenome-fppn.fr/vocabulary/2017#RGBImage
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
     * Search a sensor by uri.
     * @param String $uri searched sensor's uri
     * @return mixed YiiSensorModel : the searched sensor
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
     * create the given sensors
     * @return string the json of the creation return
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
              $sensorModel->inServiceDate = $sensor[6];
              $sensorModel->personInCharge = $sensor[8];
              
              if ($sensor[4] !== "") {
                  $sensorModel->serialNumber = $sensor[4];
              }
              if ($sensor[5] !== "") {
                  $sensorModel->dateOfPurchase = $sensor[5];
              }
              if ($sensor[7] !== "") {
                  $sensorModel->dateOfLastCalibration = $sensor[7];
              }
              
              $forWebService[] = $sensorModel->attributesToArray();
              $insertionResult = $sensorModel->insert($sessionToken, $forWebService);
              
              $sensorsUris[] = $insertionResult->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES}[0];
            }
            return json_encode($sensorsUris, JSON_UNESCAPED_SLASHES); 
        }
        return true;
    }
    
    /**
     * list all sensors
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \app\models\yiiModels\SensorSearch();
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
                'dataProvider' => $searchResult
            ]);
        }
    }
    
    /**
     * return the profile of a sensor
     * @param string $uri
     * @return array array corresponding to a sensor profile
     */
    private function getSensorProfile($uri) {
        $sensorModel = new YiiSensorModel();
        $sensorModel->getSensorProfile(Yii::$app->session['access_token'], $uri);
        return $sensorModel->properties;
    }
    
    /**
     * @action Displays a single sensor model
     * @return mixed
     */
    public function actionView($id) {
        $res = $this->findModel($id);
        
        //get sensor profile
        $res["properties"] = $this->getSensorProfile($id);
        
        //get sensor's linked documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItem = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get sensor annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $sensorAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
     
        //4. get sensors variables
        $variablesSearch = new VariableSearch();
        $variablesDataProvider = $variablesSearch->search(Yii::$app->session['access_token'], []);
        $variables = [];
        foreach ($variablesDataProvider->getModels() as $variable) {
            $variables[$variable->uri] = $variable->label;
        }

        if ($res === WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {            
            $dataSearchModel = new \app\models\yiiModels\SensorDataSearch();
            $dataSearchModel->sensorURI = $res->uri;

            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                'variables' => $variables,
                'dataSearchModel' => $dataSearchModel,
                self::ANNOTATIONS_DATA => $sensorAnnotations
            ]);
        }
    }
    
    /**
     * Ajax action to update the list of vriables measured by a sensor
     * @return the webservice result with sucess or error
     */
    public function actionUpdateVariables() {
        $post = Yii::$app->request->post();
        $sessionToken = Yii::$app->session['access_token'];        
        $sensorUri = $post["sensor"];
        if (isset($post["variables"])) {
            $variablesUri = $post["variables"];
        } else {
            $variablesUri = [];
        }
        $sensorModel = new YiiSensorModel();
        
        $res = $sensorModel->updateVariables($sessionToken, $sensorUri, $variablesUri);
        
        return json_encode($res, JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * 
     * @param string $rdfType (default null)
     * @return array the list of the sensors uris with their labels
     * e.g. 
     * [
     *      "sensor/uri" => "air_03",
     *      ...
     * ]
     */
    public function getSensorsUrisAndLabels($rdfType = null) {
        $sessionToken = Yii::$app->session['access_token'];
        $sensorSearchModel = new \app\models\yiiModels\SensorSearch();
        $sensors = null;
        
        $sensorSearchModel->rdfType = $rdfType;
        
        $sensorSearchModel->totalPages = 1;
        
        for ($i = 0; $i <= intval($sensorSearchModel->totalPages); $i++) {
            $searchParam[\app\models\wsModels\WSConstants::PAGE] = $i;
            
            $searchResult = $sensorSearchModel->search($sessionToken, $searchParam);
            
            if (is_string($searchResult)) {
                if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                    return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                } else {
                    return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
                }
            } else {
                $models = $searchResult->getmodels();
            }
            
            foreach ($models as $model) {
                $sensors[$model->uri] = $model->label;
            }
        }
        
        return $sensors;
    }
    
    /**
     * insert a lens in the database and return it's uri
     * @param array $post array with the caracteristics of the lens
     * @return string the lens uri inserted
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
        
        $requestRes = $lensModel->insert(Yii::$app->session['access_token'], $forWebService);
                
        $lensUri = $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES}[0];
        
        //2. insert the lens profile
        $lensProfile[SensorController::URI] = $lensUri;
        
        $apertureProperty[SensorController::RELATION] = Yii::$app->params[SensorController::APERTURE];
        $apertureProperty[SensorController::VALUE] = $post["lensAperture"];
        
        $lensProfileProperties[] = $apertureProperty;
        
        $focalLengthProperty[SensorController::RELATION] = Yii::$app->params[SensorController::FOCAL_LENGTH];
        $focalLengthProperty[SensorController::VALUE] = $post["lensFocalLength"];
        
        $lensProfileProperties[] = $focalLengthProperty;
        
        $lensProfile[SensorController::PROPERTIES] = $lensProfileProperties;
        $lensProfiles[] = $lensProfile;
        
        $lensModel->insertProfile(Yii::$app->session['access_token'], $lensProfiles);
                
        return $lensUri;
    }
    
    /**
     * check if a given string is part of the list of the lens properties 
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
     * extract the informations corresponding to the sensor profile from a given 
     * post and return an array with the sensor profile
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
            return \yii\helpers\ArrayHelper::map($users, 'email', 'email');            
        } else {
            return null;
        }
    }
    
    /**
     * add a sensor profile 
     * @return mixed
     */
    public function actionCharacterize() {
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
            $requestRes = $sensorModel->insertProfile(Yii::$app->session['access_token'], $sensorProfileToAdd);
            
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
            //get all the sensors types 
            //(the sensor's uris list will be updated when the user will choose a sensor type)
            $sensorsTypes = $this->getSensorsTypesSimpleAndUri();

            //get all the sensors uris (with labels)
            $sensors = $this->getSensorsUrisAndLabels();
            
            //get all users emails (for the person in charge if a lens needs to be created)
           $searchUsersModel = new \app\models\yiiModels\UserSearch();
           $users = $this->usersToMap($searchUsersModel->find(Yii::$app->session['access_token'], []));

            if (is_string($sensorsTypes) && $sensorsTypes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else if (is_string($sensorsTypes)) { //server error
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $sensorsTypes]);
            } else {
                return $this->render('characterize', [
                   'model' => $sensorModel,
                   'sensorsTypes' => $sensorsTypes,
                   'sensorsUris' => $sensors,
                   'users' => $users
                ]);
            }
        }
    }
    
    /**
     * get the list of the sensors (uri) for a given sensor type ($rdfType)
     * @param string $rdfType
     * @return json
     */
    public function actionGetSensorsUriByRdfType($rdfType) {
        $sensorsUrisAndLabels = $this->getSensorsUrisAndLabels(urldecode($rdfType));

        $sensors = null;
        
        if ($sensorsUrisAndLabels !== null) {
        
            foreach ($sensorsUrisAndLabels as $key => $value) {
                $sensors[] = ["label" => $value, "uri" => $key];
            }

            return json_encode($sensors, JSON_UNESCAPED_SLASHES);
        } else {
            return json_encode(null);
        }
    }
    
    /**
     * Get the rdfType of the given sensor uri
     * @param string $uri the sensor's uri
     * @return json the uri of the rdfType of the sensor
     */
    public function actionGetSensorsTypeByUri($uri) {        
        $sensorModel = $this->findModel($uri);
        
        if ($sensorModel->rdfType !== null) {
            return json_encode($sensorModel->rdfType, JSON_UNESCAPED_SLASHES);
        } else {
            return json_encode(null);
        }
    }
    
        /**
     * Ajax action which return the HTML graph corresponding to the SensorDataSearch POST parameters
     * @return string
     */
    public function actionSearchData() {
        $searchModel = new \app\models\yiiModels\SensorDataSearch();
        
        // Load POST parameters
        if ($searchModel->load(Yii::$app->request->post())) {
            
            // Get data
            $sessionToken = Yii::$app->session['access_token'];
            $sensorGraphData = $searchModel->getEnvironmentData($sessionToken);
            
            // Render data
            return $this->renderAjax('_view_sensor_graph', [
                'sensorGraphData' => $sensorGraphData
            ]);
        }
    }
}
