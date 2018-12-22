<?php

//**********************************************************************************************
//                                       AgronomicalObjectController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: implements the CRUD actions for YiiAgronomicalObjectModel
//***********************************************************************************************
 namespace app\controllers;
 
 use Yii;
 use yii\web\Controller;
 use yii\web\UploadedFile;
 use yii\filters\VerbFilter;
 
 use app\models\yiiModels\YiiAgronomicalObjectModel;
 use app\models\yiiModels\AgronomicalObjectSearch;
 use app\models\yiiModels\YiiExperimentModel;
 use app\models\wsModels\WSConstants;

require_once '../config/config.php';
 
/**
 * CRUD actions for YiiAgronomicalObjectModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiAgronomicalObjectModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
 class AgronomicalObjectController extends Controller {
     
     /**
      * the delim caracter for the csv files
      * @var DELIM_CSV
      */
     const DELIM_CSV = ";";
     /**
      * the geometry column for the csv files
      * @var GEOMETRY
      */
     const GEOMETRY = "Geometry";
     /**
      * the experiment uri column for the csv files
      * @var EXPERIMENT_URI
      */
     const EXPERIMENT_URI = "ExperimentURI";
     /**
      * the alias column for the csv files
      * @var ALIAS
      */
     const ALIAS = "Alias";
     /**
      * the species column for the csv files
      * @var SPECIES
      */
     const SPECIES = "Species";
     /**
      * the variety column for the csv files
      * @var VARIETY
      */
     const VARIETY = "Variety";
     /**
      * the experimental modalities column for the csv files
      * @var EXPERIMENT_MODALITIES
      */
     const EXPERIMENT_MODALITIES = "ExperimentModalities";
     /**
      * the replication column for the csv files
      * @var REPETITION
      */
     const REPETITION = "Repetition";
     
    /**
      * the replication column for the csv files
      * @var RDF_TYPE
      */
    const RDF_TYPE = "RdfType";
     
          
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
     * get the types of scientific object
     * @return array list of the obejct types uris 
     * e.g. [
     *          "UAV",
     *          "Pot"
     *      ]
     */
    public function getObjectTypes() {
        $model = new YiiAgronomicalObjectModel();
        
        $objectsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $scientificObjectConcepts = $model->getObjectTypes(Yii::$app->session['access_token']);
            if ($scientificObjectConcepts === "token") {
                return "token";
            } else {
                $totalPages = $scientificObjectConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];

                foreach ($scientificObjectConcepts[\app\models\wsModels\WSConstants::DATA] as $objectType) {
                    $objectsTypes[] = explode("#", $objectType->uri)[1];
                }
            }
        }
        
        return $objectsTypes;
    }
    
    /**
     * get the experiments
     * @return array list of species
     */
    public function getExperimentsURI() {
        $model = new YiiExperimentModel();
        $experimentsURI = [];
        $experiments = $model->getExperimentsList(Yii::$app->session['access_token']);
        if ($experiments === "token") {
            return "token";
        } else {
            foreach ($experiments as $experiment){
                $experimentsURI[] = $experiment->uri;
            }
            return $experimentsURI;
        }
    }
    
    /**
     * get the species 
     * @return array list of species
     */
    public function getSpecies() {
        $model = new YiiAgronomicalObjectModel();
        
        $species = array();
        $speciesURIList = $model->getSpeciesUriList(Yii::$app->session['access_token']);

                
        return $speciesURIList;
    }
    
    
    
    
    /**
     * get the csv file header
     * @return array list of the columns names for an agronomical objects file
     */
    private function getHeaderList() {
        return [AgronomicalObjectController::ALIAS, AgronomicalObjectController::RDF_TYPE,  
                AgronomicalObjectController::EXPERIMENT_URI, AgronomicalObjectController::GEOMETRY, 
                AgronomicalObjectController::SPECIES, AgronomicalObjectController::VARIETY, 
                AgronomicalObjectController::EXPERIMENT_MODALITIES, AgronomicalObjectController::REPETITION];
    }
    
    /**
     * 
     * @param array $csvHeader an array with for example the 
     *                         columns of a csv file
     * @return boolean true if the required columns are in the $csvHeader 
     *                 false if not
     */
    private function existRequiredColumns($csvHeader) {
        return in_array(AgronomicalObjectController::ALIAS, $csvHeader) 
                && in_array(AgronomicalObjectController::RDF_TYPE, $csvHeader) 
                && in_array(AgronomicalObjectController::EXPERIMENT_URI, $csvHeader);
    }
    
    /**
     * check if the columns names exist in the file. 
     * @param array $csvHeader the header columns list 
     * @return array if error the errors in the file 
     *               else a key value array corresponding to the columns number 
     *                    and their names in the file. 
     *                    e.g : "alias" => 3. The column alias is the third 
     *                          column in the csv file
     */
    private function getCSVHeaderCorrespondancesOrErrors($csvHeader) {
        $headersNamesNumber = null;
        $headersNames = $this->getHeaderList();
        if ($this->existRequiredColumns($csvHeader)) {
            foreach ($headersNames as $headerName) {
                $keyNumer = array_search($headerName, $csvHeader);
                if (is_int($keyNumer)) {
                    $headersNamesNumber[$headerName] = $keyNumer;
                }             }
        } else {
            $headersNamesNumber["Error"][] = Yii::t('app/messages','Required column missing');
        }
        
        return $headersNamesNumber;
    }
    
    /**
     * 
     * @param array $array
     * @return array the array without values equals to ""
     */
    private function getArrayWithoutEmptyValues($array) {
        $toReturn = null;
        foreach($array as $element) {
            if ($element != "") {
                $toReturn[] = $element;
            }
         }
         
         return $toReturn;
    }
    
    /**
     * check the geometry format. The expected format is a Polygon defined by the WKT :  
     *          POLYGON ((XX.XXX XX.XXXXX, Y.YYYYY YY.YYYY, ZZ.ZZZZZ ZZ.ZZZZ, ..., XX.XXX XX.XXXXX))
     *         On n'accepte pour l'instant que les polygonnes. 
     *         Il faudra plus tard pouvoir accepter les autres types de géométries (POINT, LINE, etc. )
     *         La vérification du type de projection (WGS84 attendu) se fera à l'insertion des données
     * @param string $geometry
     * @return true si la geometry est correcte, 
     *         false sinon
     */
    private function isGeometryOk($geometry) {        
        $explodeByOpenPar = explode("((", $geometry);
        if (count($explodeByOpenPar) === 2) {
            if (strtoupper($explodeByOpenPar[0]) === "POLYGON " 
                    || strtoupper($explodeByOpenPar[0]) === "POLYGON") {
                $explodeByClosePar = explode("))", $explodeByOpenPar[1]);
                if (count($explodeByClosePar) === 2) { // POLYGON (( XXXXXXXX ))
                    $points = explode(",", $explodeByClosePar[0]); // get polygon points
                    
                    $p1 = $this->getArrayWithoutEmptyValues(explode(" ", $points[0]));
                    $p2 = $this->getArrayWithoutEmptyValues(explode(" ", $points[(count($points)-1)]));
                    
                    if ($p1 === $p2) { //The first and the last point are le same
                        foreach ($points as $point) {
                            $latlon = $this->getArrayWithoutEmptyValues(explode(" ", $point));
                            if (count($latlon) === 2) {
                                if (floatval($latlon[0]) && floatval($latlon[1])) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * check if an experiment exist
     * @param string $experimentURI
     * @return boolean true if the experiment exist
     */
    private function existExperiment($experimentURI) {
        $experimentModel = new YiiExperimentModel(null, null);
        $experimentModel->findByURI(Yii::$app->session['access_token'], $experimentURI);
        
        return $experimentModel->uri !== null;
    }
    
    /**
     * 
     * @param string $species
     * @return boolean true if the specie uri is in the species list
     */
    private function existSpecies($species) {
        $aoModel = new YiiAgronomicalObjectModel();
        return in_array($species, $aoModel->getSpeciesUriList());
    }
    
    /**
     * check the CSV file 
     * @param array $csvContent csv contents for the agronomical objects creation
     * @return array the errors 
     *               null if no error
     */
    private function getCSVErrors($csvContent) {
        //SILEX:todo
        //create a library for the data type check (isGeometry, isDate, ...)
        //\SILEX:todo
        
        //1. check header
        $headerCheck = $this->getCSVHeaderCorrespondancesOrErrors(str_getcsv($csvContent[0], AgronomicalObjectController::DELIM_CSV));
        $errors = null;
        if (isset($headerCheck["Error"])) {
            $errors["header"] = $headerCheck["Error"];
        } else { //2. check each cell's content
            $experiments = [];
            for ($i = 1; $i < count($csvContent); $i++) {
                $row = str_getcsv($csvContent[$i], AgronomicalObjectController::DELIM_CSV);
                If ($row[$headerCheck["Geometry"]] != "") {
                    if (!$this->isGeometryOk($row[$headerCheck["Geometry"]])) {
                        $error = null;
                        $error["line"] = "L." . ($i + 1);
                        $error["column"] = AgronomicalObjectController::GEOMETRY;
                        $error["message"] = Yii::t('app/messages', 'Bad geometry given') . ". " . Yii::t('app/messages', 'Expected format') . " : POLYGON ((1.33 2.33, 3.44 5.66, 4.55 5.66, 6.77 7.88, 1.33 2.33))";
                        $errors[] = $error;
                        }
                }
                if (!in_array($row[$headerCheck[AgronomicalObjectController::EXPERIMENT_URI]], $experiments)) {
                    if (!$this->existExperiment($row[$headerCheck[AgronomicalObjectController::EXPERIMENT_URI]])) {                        
                        $error = null;
                        $error["line"] = "L." . ($i + 1);
                        $error["column"] = AgronomicalObjectController::EXPERIMENT_URI;
                        $error["message"] = Yii::t('app/messages', 'Unknown experiment') . " : " . $row[$headerCheck[AgronomicalObjectController::EXPERIMENT_URI]];
                        $errors[] = $error;
                    }
                    $experiments[] = $row[$headerCheck[AgronomicalObjectController::EXPERIMENT_URI]];
                }               
                if (!$this->existSpecies($row[$headerCheck["Species"]])) {
                    $error = null;
                    $error["line"] = "L." . ($i + 1);
                    $error["column"] = AgronomicalObjectController::SPECIES;
                    $error["message"] = Yii::t('app/messages', 'Unknown species') . " : " . $row[$headerCheck[AgronomicalObjectController::SPECIES]];
                    $errors[] = $error;
                }
                if ($row[$headerCheck["Alias"]] == "") {
                    $error = null;
                    $error["line"] = "L." . ($i + 1);
                    $error["column"] = AgronomicalObjectController::ALIAS;
                    $error["message"] = Yii::t('app/messages', 'Alias is missing');
                    $errors[] = $error;
                }
            }
        }
        
        return $errors;      
    }
    
    /**
     * get a message html error to show with the errors founded in the csv file
     * @param array $arrayError errors. Expected format :
     *                                     ["L.85"]["Geometry"]["Error message"]
     * @return string the message to show to the user 
     */
    private function getErrorMessageToPrint($arrayError) {
        if (isset($arrayError["header"])) {
            $errorMessage = "<div class=\"alert alert-danger\" role=\"alert\"><b>" . $arrayError["header"][0] . "</b></div>";
        } else {
            $errorMessage = "<div class=\"alert alert-danger\" role=\"alert\"><b>" . Yii::t('app/messages', 'Errors in file') . "</b>"
                            . "<table class=\"table table-hover\">"
                            . "<thead><tr><th>" . Yii::t('app', 'Line') . "</th><th>" . Yii::t('app', 'Column') . "</th><th>" . Yii::t('app', 'Error') . "</th></tr></thead><tbody>";

            foreach ($arrayError as $errorLine) {
                $errorMessage .= "<tr>";
                $errorMessage .= "<th scope=\"row\"><p>" .$errorLine["line"] . "</p></th>";
                $errorMessage .= "<td>" .$errorLine["column"] . "</td>";
                $errorMessage .= "<td>" .$errorLine["message"] . "</td>";
                $errorMessage .= "</tr>";
            }
            $errorMessage .= "</tbody></table></div>";
        }
        
        return $errorMessage;
    }
    
    /**
     * check if a string value is empty or not
     * @param string $value
     * @return boolean true if not empty
     */
    private function valueIsNotEmpty($value) {
        return $value !== ""
            && $value !== " "
            && $value !== "\t"
            && !empty($value);
    }
    

    

    /**
     * generated the scientific object creation page
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiAgronomicalObjectModel();
        
        $objectsTypes = $this->getObjectTypes();
        if ($objectsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $experiments = $this->getExperimentsURI();
        if ($experiments === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }        
        
        $species = $this ->getSpecies();
        
        return $this->render('create', [
            'model' => $model,
            'objectsTypes' => json_encode($objectsTypes, JSON_UNESCAPED_SLASHES),
            'experiments' => json_encode($experiments, JSON_UNESCAPED_SLASHES),
            'species' => json_encode($species,JSON_UNESCAPED_SLASHES)
        ]);
    }
    
    /**
     * create the given objects
     * @return string the json of the creation return
     */
    public function actionCreateMultipleScientificObjects() {
        $objects = json_decode(Yii::$app->request->post()["objects"]);
        $sessionToken = Yii::$app->session['access_token'];
        
        $objectsUris = null;
        if (count($objects) > 0) {
            $objectsUris = null;
            foreach ($objects as $object) {
                $forWebService = null;
                $scientificObjectModel = new YiiAgronomicalObjectModel();
                
                $scientificObjectModel->alias = $object[1];
                $scientificObjectModel->type = $this->getObjectTypeCompleteUri($object[2]);
                $scientificObjectModel->experiment = $object[3];
                $scientificObjectModel->geometry = $object[4];
                $scientificObjectModel->parent = $object[5];
                $scientificObjectModel->species = $object[6];
                $scientificObjectModel->variety = $object[7];                
                
                $scientificObject = $scientificObjectModel->attributesToArray();
                $forWebService = $this->getArrayForWebServiceCreate($scientificObject);
                $insertionResult = $scientificObjectModel->insert($sessionToken, $forWebService);
                
                $objectsUris[] = $insertionResult->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES}[0];
            }
            return json_encode($objectsUris, JSON_UNESCAPED_SLASHES); 
        }
        
        return true;
    }
    
    /**
     * 
     * @param string $vectorType
     * @return string the complete vector type uri corresponding to the given 
     *                vector type
     *                e.g. http://www.phenome-fppn.fr/vocabulary/2017#UAV
     */
    private function getObjectTypeCompleteUri($objectType) {
        $objectTypesList = $this->getObjectsTypesUris();
        foreach ($objectTypesList as $objectTypeUri) {
            if (strpos($objectTypeUri, $objectType)) {
                return $objectTypeUri;
            }
        }
        return null;
    }
    
        /**
     * get the vectors types (complete uri)
     * @return array list of the vectors types uris 
     * e.g. [
     *          "http://www.phenome-fppn.fr/vocabulary/2017#UAV",
     *          "http://www.phenome-fppn.fr/vocabulary/2017#Pot"
     *      ]
     */
    public function getObjectsTypesUris() {
        $model = new YiiAgronomicalObjectModel();
        
        $objectsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $objectsConcepts = $model->getObjectTypes(Yii::$app->session['access_token']);
            if ($objectsConcepts === "token") {
                return "token";
            } else {
                $totalPages = $objectsConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];
                foreach ($objectsConcepts[\app\models\wsModels\WSConstants::DATA] as $objectType) {
                    $objectsTypes[] = $objectType->uri;
                }
            }
        }
        
        return $objectsTypes;
    }
    
    /**
     * 
     * @param array $experiments
     * @return array key : experiment uri, value : experiment alias. 
     *               e.g "http://experiment/uri" => "experimentAlias"
     */
    private function experimentsToMap($experiments) {
        if ($experiments !== null) {
            return \yii\helpers\ArrayHelper::map($experiments, 'uri', 'alias');
        } else {
            return null;
        }
    }
    
        /**
     * @update Dec. 2018 : the geometry becomes facultative and it is required to define the rdfType
     * @param array $fileContent the csv file content
     * @param array $correspondances the columns numbers corresponding to the 
     *                               expected columns (if the file columns are 
     *                               not in the good order) 
     * @return array data of the attribute $fileContent 
     *               in the web service expected format
     */
    private function getArrayForWebServiceCreate($scientificObject) {
        
        if ($scientificObject["alias"] != null) {
            $alias["relation"] = Yii::$app->params['rdfsLabel'];
            $alias["value"] = $scientificObject["alias"];
            $p["properties"][] = $alias;
        }
        
        $p["rdfType"] = $scientificObject["type"];
        $p["experiment"] = $scientificObject["experiment"];
        $p["geometry"] = $scientificObject["geometry"];
        
        if ($scientificObject["ispartof"] != null) {
            $parent["relation"] = Yii::$app->params['isPartOf'];
            $parent["value"] = $scientificObject["ispartof"];
            $p["properties"][] = $parent;
        }
        
        if ($scientificObject["species"] != null) {
            $species["rdfType"] = Yii::$app->params['Species'];
            $species["relation"] = Yii::$app->params['fromSpecies'];
            $species["value"] = $scientificObject["species"];
            $p["properties"][] = $species;
        }
        
        if ($scientificObject["variety"] != null) {
            $variety["rdfType"] = Yii::$app->params['Variety'];
            $variety["relation"] = Yii::$app->params['fromVariety'];
            $value = str_replace(" ", "_", $scientificObject["variety"]);
            $variety["value"] = $value;
            $p["properties"][] = $variety;
        }
       
        $forWebService[] = $p;    

        return $forWebService;
    }
    
    
    
    /**
     * agronomical objects index (list of agronomical objects)
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AgronomicalObjectSearch();
        
        //Get the search params and update the page if needed
        $searchParams = Yii::$app->request->queryParams;        
        if (isset($searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE]--;
        }
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            //Récupération de la liste des expérimentations
            //SILEX:TODO
            // ATTENTION : Il faudra ajouter la gestion de la pagination pour la récupération de la liste des expérimentations
            //\SILEX:TODO
            $searchExperimentModel = new \app\models\yiiModels\ExperimentSearch();
            $experiments = $searchExperimentModel->find(Yii::$app->session['access_token'], []);
            $experiments = $this->experimentsToMap($experiments);
            $this->view->params['listExperiments'] = $experiments;
            
            return $this->render('index', [
               'searchModel' => $searchModel,
                'dataProvider' => $searchResult
            ]);
        }
    }
    
    /**
     * allows the user to download the csv of a search agronomical objects 
     * result on the index page
     * @return mixed 
     */
    public function actionDownloadCsv() {
        if (isset($_GET['model'])) {
            $searchParams = $_GET['model'];
        } else {
            $searchParams = [];
        }
        $searchModel = new AgronomicalObjectSearch();
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            //get all the data (if multiple pages) and write them in a file
            $serverFilePath = \config::path()['documentsUrl'] . "AOFiles/exportedData/" . time() . ".csv";
            
            $headerFile = "ScientificObjectURI" . AgronomicalObjectController::DELIM_CSV .
                          "Alias" . AgronomicalObjectController::DELIM_CSV .
                          "RdfType" . AgronomicalObjectController::DELIM_CSV .
                          "ExperimentURI" . AgronomicalObjectController::DELIM_CSV . 
                          "\n";
            
            file_put_contents($serverFilePath, $headerFile);
            
            for ($i = 0; $i <= intval($searchModel->totalPages); $i++) {
                //1. call service for each page
                $searchParams["page"] = $i;
                
                //SILEX:TODO
                //Find why the $this->load does not work in this case in the search
                $searchModel->experiment = isset($_GET['model']["uri"]) ? $_GET['model']["uri"] : null;
                $searchModel->experiment = isset($_GET["model"]["alias"]) ? $_GET["model"]["alias"] : null;
                $searchModel->experiment = isset($_GET['model']['experiment']) ? $_GET['model']['experiment'] : null;
                //\SILEX:TODO
                $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
                                
                //2. write in file
                $models = $searchResult->getmodels();
                
                foreach ($models as $model) {
                    $stringToWrite = $model->uri . AgronomicalObjectController::DELIM_CSV . 
                                     $model->alias . AgronomicalObjectController::DELIM_CSV .
                                     $model->rdfType . AgronomicalObjectController::DELIM_CSV .
                                     $model->experiment . AgronomicalObjectController::DELIM_CSV . 
                                     "\n";
                    
                    file_put_contents($serverFilePath, $stringToWrite, FILE_APPEND);
                }
            }
            Yii::$app->response->sendFile($serverFilePath); 
        }
    }
 }
