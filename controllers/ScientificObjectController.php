<?php

//**********************************************************************************************
//                                       ScientificObjectController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: implements the CRUD actions for YiiScientificObjectModel
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\yiiModels\YiiScientificObjectModel;
use app\models\yiiModels\ScientificObjectSearch;
use app\models\yiiModels\YiiExperimentModel;

require_once '../config/config.php';

/**
 * CRUD actions for YiiScientificObjectModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiScientificObjectModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class ScientificObjectController extends Controller {

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
     * @var REPLICATION
     */
    const REPLICATION = "Replication";

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
        $model = new YiiScientificObjectModel();

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
     * @return array list of experiments
     */
    public function getExperiments() {
        $model = new YiiExperimentModel();
        return $model->getExperimentsURIAndLabelList(Yii::$app->session['access_token']);
    }

    /**
     * get the species 
     * @return array list of species
     */
    public function getSpecies() {
        $speciesModel = new \app\models\yiiModels\YiiSpeciesModel();
        return $speciesModel->getSpeciesUriLabelList(Yii::$app->session['access_token']);
    }

    /**
     * get the csv file header
     * @return array list of the columns names for a scientific objects file
     */
    private function getHeaderList() {
        return [ScientificObjectController::ALIAS, ScientificObjectController::RDF_TYPE,
            ScientificObjectController::EXPERIMENT_URI, ScientificObjectController::GEOMETRY,
            ScientificObjectController::SPECIES, ScientificObjectController::VARIETY,
            ScientificObjectController::EXPERIMENT_MODALITIES, ScientificObjectController::REPLICATION];
    }

    /**
     * 
     * @param array $csvHeader an array with for example the 
     *                         columns of a csv file
     * @return boolean true if the required columns are in the $csvHeader 
     *                 false if not
     */
    private function existRequiredColumns($csvHeader) {
        return in_array(ScientificObjectController::ALIAS, $csvHeader) && in_array(ScientificObjectController::RDF_TYPE, $csvHeader) && in_array(ScientificObjectController::EXPERIMENT_URI, $csvHeader);
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
                }
            }
        } else {
            $headersNamesNumber["Error"][] = Yii::t('app/messages', 'Required column missing');
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
        foreach ($array as $element) {
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
            if (strtoupper($explodeByOpenPar[0]) === "POLYGON " || strtoupper($explodeByOpenPar[0]) === "POLYGON") {
                $explodeByClosePar = explode("))", $explodeByOpenPar[1]);
                if (count($explodeByClosePar) === 2) { // POLYGON (( XXXXXXXX ))
                    $points = explode(",", $explodeByClosePar[0]); // get polygon points

                    $p1 = $this->getArrayWithoutEmptyValues(explode(" ", $points[0]));
                    $p2 = $this->getArrayWithoutEmptyValues(explode(" ", $points[(count($points) - 1)]));

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
        $aoModel = new YiiScientificObjectModel();
        return in_array($species, $aoModel->getSpeciesUriList());
    }

    /**
     * check the CSV file 
     * @param array $csvContent csv contents for the scientific objects creation
     * @return array the errors 
     *               null if no error
     */
    private function getCSVErrors($csvContent) {
        //SILEX:todo
        //create a library for the data type check (isGeometry, isDate, ...)
        //\SILEX:todo
        //1. check header
        $headerCheck = $this->getCSVHeaderCorrespondancesOrErrors(str_getcsv($csvContent[0], ScientificObjectController::DELIM_CSV));
        $errors = null;
        if (isset($headerCheck["Error"])) {
            $errors["header"] = $headerCheck["Error"];
        } else { //2. check each cell's content
            $experiments = [];
            for ($i = 1; $i < count($csvContent); $i++) {
                $row = str_getcsv($csvContent[$i], ScientificObjectController::DELIM_CSV);
                If ($row[$headerCheck["Geometry"]] != "") {
                    if (!$this->isGeometryOk($row[$headerCheck["Geometry"]])) {
                        $error = null;
                        $error["line"] = "L." . ($i + 1);
                        $error["column"] = ScientificObjectController::GEOMETRY;
                        $error["message"] = Yii::t('app/messages', 'Bad geometry given') . ". " . Yii::t('app/messages', 'Expected format') . " : POLYGON ((1.33 2.33, 3.44 5.66, 4.55 5.66, 6.77 7.88, 1.33 2.33))";
                        $errors[] = $error;
                    }
                }
                if (!in_array($row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]], $experiments)) {
                    if (!$this->existExperiment($row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]])) {
                        $error = null;
                        $error["line"] = "L." . ($i + 1);
                        $error["column"] = ScientificObjectController::EXPERIMENT_URI;
                        $error["message"] = Yii::t('app/messages', 'Unknown experiment') . " : " . $row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]];
                        $errors[] = $error;
                    }
                    $experiments[] = $row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]];
                }
                if (!$this->existSpecies($row[$headerCheck["Species"]])) {
                    $error = null;
                    $error["line"] = "L." . ($i + 1);
                    $error["column"] = ScientificObjectController::SPECIES;
                    $error["message"] = Yii::t('app/messages', 'Unknown species') . " : " . $row[$headerCheck[ScientificObjectController::SPECIES]];
                    $errors[] = $error;
                }
                if ($row[$headerCheck["Alias"]] == "") {
                    $error = null;
                    $error["line"] = "L." . ($i + 1);
                    $error["column"] = ScientificObjectController::ALIAS;
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
                $errorMessage .= "<th scope=\"row\"><p>" . $errorLine["line"] . "</p></th>";
                $errorMessage .= "<td>" . $errorLine["column"] . "</td>";
                $errorMessage .= "<td>" . $errorLine["message"] . "</td>";
                $errorMessage .= "</tr>";
            }
            $errorMessage .= "</tbody></table></div>";
        }

        return $errorMessage;
    }

    /**
     * generated the scientific object creation page
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiScientificObjectModel();

        $objectsTypes = $this->getObjectTypes();
        if ($objectsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $experiments = $this->getExperiments();
        if ($experiments === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }

        $species = $this->getSpecies();

        return $this->render('create', [
                    'model' => $model,
                    'objectsTypes' => json_encode($objectsTypes, JSON_UNESCAPED_SLASHES),
                    'experiments' => json_encode(array_values($experiments), JSON_UNESCAPED_SLASHES),
                    'species' => json_encode(array_values($species), JSON_UNESCAPED_SLASHES)
        ]);
    }

    /**
     * create the given objects
     * @return string the json of the creation return
     */
    public function actionCreateMultipleScientificObjects() {
        $objects = json_decode(Yii::$app->request->post()["objects"]);
        $sessionToken = Yii::$app->session['access_token'];

        $return = [
            "objectUris" => [],
            "messages" => []
        ];

        $experiments = $this->getExperiments();
        if ($experiments === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }

        $species = $this->getSpecies();

        if (count($objects) > 0) {
            $objectsToInsert = count($objects);
            $cpt = 0;
            $forWebService = [];
            foreach ($objects as $object) {
                $scientificObjectModel = new YiiScientificObjectModel();

                $scientificObjectModel->label = $object[1];
                $scientificObjectModel->type = $this->getObjectTypeCompleteUri($object[2]);
                $scientificObjectModel->experiment = array_search($object[3], $experiments);
                $scientificObjectModel->geometry = $object[4];
                $scientificObjectModel->parent = $object[5];
                $scientificObjectModel->species = array_search($object[6], $species);
                $scientificObjectModel->variety = $object[7];
                $scientificObjectModel->modality = $object[8];
                $scientificObjectModel->replication = $object[9];

                $scientificObject = $scientificObjectModel->attributesToArray();

                $forWebService[] = $this->getArrayForWebServiceCreate($scientificObject);
                $cpt++;
                //Insert the scientific objects by 200
                if ($cpt === 200 || $cpt === $objectsToInsert) {
                    $objectsToInsert = $objectsToInsert - $cpt;
                    $cpt = 0;

                    $insertionResult = $scientificObjectModel->insert($sessionToken, $forWebService);

                    if ($insertionResult->{\app\models\wsModels\WSConstants::METADATA}->status[0]->exception->type != "Error") {
                        foreach ($insertionResult->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES} as $scientificObjectUri) {
                            $return["objectUris"][] = $scientificObjectUri;
                            $return["messages"][] = "object saved";
                        }
                    } else {
                        foreach ($insertionResult->{\app\models\wsModels\WSConstants::METADATA}->status as $status) {
                            $return["objectUris"][] = null;
                            $return["messages"][] = $status->exception->details;
                        }
                    }
                }
            }
        }

        return json_encode($return, JSON_UNESCAPED_SLASHES);
    }

    /**
     * 
     * @param string $vectorType
     * @return string the complete vector type uri corresponding to the given 
     *                vector type
     *                e.g. http://www.opensilex.org/vocabulary/oeso#UAV
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
     *          "http://www.opensilex.org/vocabulary/oeso#UAV",
     *          "http://www.opensilex.org/vocabulary/oeso#Pot"
     *      ]
     */
    public function getObjectsTypesUris() {
        $model = new YiiScientificObjectModel();

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

        if ($scientificObject[YiiScientificObjectModel::ALIAS] != null) {
            $alias["relation"] = Yii::$app->params['rdfsLabel'];
            $alias["value"] = $scientificObject[YiiScientificObjectModel::ALIAS];
            $p["properties"][] = $alias;
        }

        $p["rdfType"] = $scientificObject["rdfType"];
        $p["experiment"] = $scientificObject["experiment"];
        $p["geometry"] = $scientificObject["geometry"];

        if ($scientificObject["ispartof"] != null) {
            $parent["relation"] = Yii::$app->params['isPartOf'];
            $parent["value"] = $scientificObject["ispartof"];
            $p["properties"][] = $parent;
        }

        if ($scientificObject["species"] != null) {
            $species["rdfType"] = Yii::$app->params['Species'];
            $species["relation"] = Yii::$app->params['hasSpecies'];
            $species["value"] = $scientificObject["species"];
            $p["properties"][] = $species;
        }

        if ($scientificObject["variety"] != null) {
            $variety["rdfType"] = Yii::$app->params['Variety'];
            $variety["relation"] = Yii::$app->params['hasVariety'];
            $value = str_replace(" ", "_", $scientificObject["variety"]);
            $variety["value"] = $value;
            $p["properties"][] = $variety;
        }

        if ($scientificObject[YiiScientificObjectModel::MODALITY] !== null) {
            $modality["relation"] = Yii::$app->params['hasExperimentModalities'];
            $modality["value"] = $scientificObject[YiiScientificObjectModel::MODALITY];
            $p["properties"][] = $modality;
        }

        if ($scientificObject[YiiScientificObjectModel::REPLICATION] !== null) {
            $replication["relation"] = Yii::$app->params['hasReplication'];
            $replication["value"] = $scientificObject[YiiScientificObjectModel::REPLICATION];
            $p["properties"][] = $replication;
        }

        return $p;
    }

    /**
     * Ajax call from index view : an sci. obj. or all sci. obj. from the page are add to the cart (session variable)
     * @return the count of the cart
     * 
     */
    public function actionAddToCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        $items = Yii::$app->request->post()["items"];
        if ($items) {
            if (isset($session['cart'])) {
                $temp = $session['cart'];

                foreach ($items as $item) {
                    if (!in_array($item, $temp)) {
                        $temp[] = $item;
                    }
                }
                $session['cart'] = $temp;
            } else {

                $session['cart'] = $items;
            }
        }

        return ['totalCount' => count($session['cart'])];
    }

  

    /**
     * Ajax call from index view : all sci. obj.  are add to the cart (session variable)
     * @return the count of the cart
     * 
     */
    public function actionAllToAddToCart() {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;

        $items = $this->getUriObjectList(Yii::$app->request->post()["alias"], Yii::$app->request->post()["type"], Yii::$app->request->post()["experiment"], Yii::$app->session['access_token']);
        $session['allcart'] = $items;
        if ($items) {
            if (isset($session['cart'])) {
                $temp = $session['cart'];

                foreach ($items as $item) {
                    if (!in_array($item, $temp)) {
                        $temp[] = $item;
                    }
                }
                $session['cart'] = $temp;
            } else {
                $session['cart'] = $items;
            }
        }
        return ['totalCount' => count( $session['cart'])];
    }
    
      /**
     * Ajax call from index view : an sci. obj. or all sci. obj. from the page are removed from the cart (session variable)
     * @return the count of the cart
     * 
     */
    public function actionRemoveFromCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        if (Yii::$app->request->post()["items"]) {

            $temp = $session['cart'];
            $items = Yii::$app->request->post()["items"];
            $temp = array_diff($temp, $items);
            $session['cart'] = $temp;
            if (count($items) > 1) {
                return ['totalCount' => count($session['cart']),
                    'allPageSelected' => "o"];
            }
            return ['totalCount' => count($session['cart'])];
        }
    }

    /**
     * Ajax call from index view : all sci. obj.  are removed from the cart (session variable)
     * @return the count of the cart
     * 
     */
    public function actionAllToRemoveFromCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        $allcarts=$session['allcart'];
        $temp = $session['cart'];
        $temp = array_diff($temp, $allcarts);
        $session['cart'] = $temp;
        return ['totalCount' => count($session['cart'])];
    }

    /**
     * scientific objects index (list of scientific objects)
     * @return mixed
     */
    public function actionIndex() {
        //TEST
        // Get the selected obj.sci from the session 
        //TEST

        $searchModel = new ScientificObjectSearch();

        //Get the search params and update the page if needed
        $searchParams = Yii::$app->request->queryParams;
        if (isset($searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE] --;
        }

        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);

        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN_INVALID) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                            'name' => Yii::t('app/messages', 'Internal error'),
                            'message' => $searchResult]);
            }
        } else {
            //Get the experiments list
            $experimentModel = new YiiExperimentModel();
            $this->view->params['listExperiments'] = $experimentModel->getExperimentsURIAndLabelList(Yii::$app->session['access_token']);

            //Get all the types of scientific objects
            $objectsTypes = $this->getObjectsTypesUris();
            if ($objectsTypes === "token") {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }

            //Prepare the array for the select of the view
            $scientificObjectsTypesToReturn = [];
            foreach ($objectsTypes as $objectType) {
                $scientificObjectsTypesToReturn[$objectType] = explode("#", $objectType)[1];
            }
            $session = Yii::$app->session;

            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        'scientificObjectTypes' => $scientificObjectsTypesToReturn,
                        'total' => count($session['cart']),
                        'cart' => $session['cart'],
                        'searchParams' => $searchParams
            ]);
        }
    }

    /**
     * Function to select all the filtered sci. obj.
     * @param type $label
     * @param type $type
     * @param type $experiment
     * @param type $token
     * @return array of uri
     * 
     */
    public function getUriObjectList($label, $type, $experiment, $token) {

        $searchModel = new ScientificObjectSearch();
        $searchModel->label = isset($label) ? $label : null;
        $searchModel->type = isset($type) ? $type : null;
        $searchModel->experiment = isset($experiment) ? $experiment : null;
        $searchParams = []; // ???
        // Set page size to 10000 for better performances
        $searchModel->pageSize = 10000;
        $totalPage = 1;
        $items = array();
        for ($i = 0; $i < $totalPage; $i++) {
            //1. call service for each page
            $searchParams["page"] = $i;

            $searchResult = $searchModel->search($token, $searchParams);

            //2. write sci. obj in array
            $models = $searchResult->getmodels();

            foreach ($models as $model) {
                $items[] = $model->uri;
            }

            $totalPage = intval($searchModel->totalPages);
        }
        return $items;
    }

    /**
     * allows the user to download the csv of a search scientific objects 
     * result on the index page
     * @return mixed 
     */
    public function actionDownloadCsv() {
        $searchModel = new ScientificObjectSearch();
        if (isset($_GET['model'])) {
            $searchParams = $_GET['model'];
            $searchModel->label = isset($searchParams["alias"]) ? $searchParams["alias"] : null;  //why alias ? and not label , c'est quoi l'alias ?
            $searchModel->type = isset($searchParams["type"]) ? $searchParams["type"] : null;
            $searchModel->experiment = isset($searchParams["experiment"]) ? $searchParams["experiment"] : null;
        }
        $searchParams = []; // ???
        // Set page size to 10000 for better performances
        $searchModel->pageSize = 10000;

        //get all the data (if multiple pages) and write them in a file
        $serverFilePath = \config::path()['documentsUrl'] . "AOFiles/exportedData/" . time() . ".csv";
        $stringToWrite = "ScientificObjectURI" . ScientificObjectController::DELIM_CSV .
                "Alias" . ScientificObjectController::DELIM_CSV .
                "RdfType" . ScientificObjectController::DELIM_CSV .
                "ExperimentURI" . ScientificObjectController::DELIM_CSV .
                "Geometry" . ScientificObjectController::DELIM_CSV .
                "\n";

        $totalPage = 1;
        for ($i = 0; $i < $totalPage; $i++) {
            //1. call service for each page
            $searchParams["page"] = $i;

            $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);

            //2. write in file
            $models = $searchResult->getmodels();

            foreach ($models as $model) {
                // Parse geoJson geometry to WKT if exists
                $geoJson = $model->geometry;
                if ($geoJson != null) {
                    $geom = \geoPHP::load($model->geometry, 'json');
                    $wktGeometry = (new \WKT())->write($geom);
                } else {
                    $wktGeometry = "";
                }
                $stringToWrite .= $model->uri . ScientificObjectController::DELIM_CSV .
                        $model->label . ScientificObjectController::DELIM_CSV .
                        $model->rdfType . ScientificObjectController::DELIM_CSV .
                        $model->experiment . ScientificObjectController::DELIM_CSV .
                        '"' . $wktGeometry . '"' . ScientificObjectController::DELIM_CSV .
                        "\n";
            }

            $totalPage = intval($searchModel->totalPages);
        }
        file_put_contents($serverFilePath, $stringToWrite, FILE_APPEND);
        Yii::$app->response->sendFile($serverFilePath);
    }

    /**
     * Generated the scientific object update page.
     * @return mixed
     */
    public function actionUpdate() {
        $sessionToken = Yii::$app->session['access_token'];
        $model = new YiiScientificObjectModel();

        $objectsTypes = $this->getObjectTypes();
        if ($objectsTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $experiments = $this->getExperiments();
        if ($experiments === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }

        $species = $this->getSpecies();

        return $this->render('update', [
                    'model' => $model,
                    'objectsTypes' => json_encode($objectsTypes, JSON_UNESCAPED_SLASHES),
                    'experiments' => json_encode(array_values($experiments), JSON_UNESCAPED_SLASHES),
                    'species' => json_encode(array_values($species), JSON_UNESCAPED_SLASHES)
        ]);
    }

    /**
     * Update the given objects
     * @return string the json of the creation return
     */
    public function actionUpdateMultipleScientificObjects() {
        $objects = json_decode(Yii::$app->request->post()["objects"]);
        $sessionToken = Yii::$app->session['access_token'];

        $return = [
            "error" => false,
            "objectUris" => [],
            "messages" => []
        ];

        $experiments = $this->getExperiments();
        if ($experiments === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }

        $species = $this->getSpecies();

        if (count($objects) > 0) {

            foreach ($objects as $object) {
                $scientificObjectModel = new YiiScientificObjectModel();
                $uri = $object[0];
                $scientificObjectModel->label = $object[1];
                $scientificObjectModel->type = $this->getObjectTypeCompleteUri($object[2]);
                $experiment = array_search($object[3], $experiments);
                $scientificObjectModel->geometry = $object[4];
                $scientificObjectModel->parent = $object[5];
                $scientificObjectModel->species = array_search($object[6], $species);
                $scientificObjectModel->variety = $object[7];
                $scientificObjectModel->modality = $object[8];
                $scientificObjectModel->replication = $object[9];

                $insertionResult = $scientificObjectModel->updateByExperiment($sessionToken, $uri, $experiment);

                if ($insertionResult === \app\models\wsModels\WSConstants::TOKEN) {
                    return $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
                } else if (isset($insertionResult->metadata->status[0]->exception->type) && $insertionResult->metadata->status[0]->exception->type !== "Error") {
                    $return["objectUris"][] = $insertionResult->metadata->datafiles[0];
                    $return["messages"][] = "object updated";
                } else {
                    $return["error"] = true;
                    $return["objectUris"][] = null;
                    $return["messages"][] = $insertionResult->metadata->status[0]->exception->details;
                }
            }
        }
        return json_encode($return, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generates the page to visualize data about a scientific object.
     * SILEX:info
     * the label and experiment parameter will have to be removed 
     * when the /scientificObject/{uri} GET will be done in the web service.
     * \SILEX:info
     * @param type $uri
     * @param type $label
     * @param type $experimentUri
     * @return mixed the visualization page
     */
    public function actionDataVisualization($uri, $label, $experimentUri = null) {
        $token = Yii::$app->session['access_token'];
        $show = false;
        $scientificObject = new YiiScientificObjectModel();
        $scientificObject->uri = $uri;
        $scientificObject->label = $label;
        $scientificObject->experiment = $experimentUri;

        //Get the list of the variables
        $variables = [];

        //If the experiment URI is empty, we get all the variables. 
        if (empty($experimentUri)) {
            $variableModel = new \app\models\yiiModels\YiiVariableModel();
            $variables = $variableModel->getInstancesDefinitionsUrisAndLabel($token);
        } else { //There is an experiment. Get the variables linked to the experiment.
            $experimentModel = new YiiExperimentModel();
            $variables = $experimentModel->getMeasuredVariables($token, $scientificObject->experiment);
        }

        // Load existing provenances
        $provenanceService = new \app\models\wsModels\WSProvenanceModel();
        $provenances = $this->mapProvenancesByUri($provenanceService->getAllProvenances($token));
        $this->view->params["provenances"] = $provenances;
        // Load images type
        $imageModel = new \app\models\yiiModels\YiiImageModel();
        $this->view->params["imagesType"] = $imageModel->getRdfTypes($token);


        //Search data for the scientific object and the given variable.
        if (isset($_POST['variable'])) {
            $toReturn = [];
            $searchModel = new \app\models\yiiModels\DataSearchLayers();
            $searchModel->pageSize = 80000;
            $searchModel->object = $scientificObject->uri;

            $searchModel->variable = $_POST['variable'];
            $searchModel->startDate = $_POST['dateStart'];
            $searchModel->endDate = $_POST['dateEnd'];
            $searchModel->provenance = $_POST['provenances'];
            $searchResult = $searchModel->search($token, null);

            /* Build array for highChart
             * e.g : 
             * {
             *   "variable": "http:\/\/www.opensilex.org\/demo\/id\/variable\/v0000001",
             *   "scientificObjectData": [
             *          "label": "Scientific object label",
             *          "data": [["1,874809","2015-02-10"],
             *                   ["2,313261","2015-03-15"]
             *    ]
             *  }]
             * }
             */

            /* Build array for highChart
             * e.g : 
             * {
             *   "variable": "http:\/\/www.opensilex.org\/demo\/id\/variable\/v0000001",
             *   "scientificObjectData": [
             *          "label": "Scientific object label",
             *          "dataFromProvenance": [
             *                     "provenance":"Data provenance uri",
             *                     "data": ["1,874809","2015-02-10"],
             *                             ["2,313261","2015-03-15"],..
             *    ]
             *  ]
             * }
             */

            $data = [];
            $scientificObjectData["label"] = $label;
            $one = true;
            foreach ($searchResult->getModels() as $model) {
                if (!empty($model->value)) {
                    $dataToSave = null;
                    $dataToSave["provenanceUri"] = $model->provenanceUri;
                    if ($one) {
                        $datestring = $model->date;
                        $strtotime = strtotime($model->date);
                        $one = false;
                    }

                    $dataToSave["date"] = (strtotime($model->date)) * 1000; //need the * 1000 because PHP uses epoch time in seconds, Javascript uses milliseconds.
                    $dataToSave["value"] = doubleval($model->value);
                    $data[] = $dataToSave;
                }
            }
            // Transform to map based to the provenance value

            $dataByProvenance = array();
            foreach ($data as $dataEl) {
                $dataByProvenanceToSave = null;
                $dataByProvenanceToSave[] = $dataEl['date'];
                $dataByProvenanceToSave[] = $dataEl['value'];
                $dataByProvenance[$dataEl['provenanceUri']][] = $dataByProvenanceToSave;
            }

            if (!empty($data)) {
                $toReturn["variable"] = $searchModel->variable;
                $scientificObjectData["dataFromProvenance"] = $dataByProvenance;
                $toReturn["scientificObjectData"][] = $scientificObjectData;
            }



            //on FORM submitted:
            //check if image visualization is activated
            $show = isset($_POST['show']) ? $_POST['show'] : null;
            $selectedVariable = isset($_POST['variable']) ? $_POST['variable'] : null;
            $imageTypeSelected = isset($_POST['imageType']) ? $_POST['imageType'] : null;
            $selectedProvenance = isset($_POST['provenances']) ? $_POST['provenances'] : null;
            $selectedPosition = isset($_POST['position']) ? $_POST['position'] : null;
            if (isset($_POST['position']) && $_POST['position'] !== "") {
                $filterToSend = "{'metadata.position':'" . $_POST['position'] . "'}";
            }
            return $this->render('data_visualization', [
                        'model' => $scientificObject,
                        'variables' => $variables,
                        'data' => $toReturn,
                        'show' => $show,
                        'dateStart' => $searchModel->startDate,
                        'dateEnd' => $searchModel->endDate,
                        'selectedVariable' => $selectedVariable,
                        'imageTypeSelected' => $imageTypeSelected,
                        'selectedProvenance' => $selectedProvenance,
                        'selectedPosition' => $selectedPosition,
                        'filterToSend' => $filterToSend,
                        'datestring' => $datestring,
                        'strtotime' => $strtotime,
            ]);
        } else { //If there is no variable given, just redirect to the visualization page.
            return $this->render('data_visualization', [
                        'model' => $scientificObject,
                        'variables' => $variables
            ]);
        }
    }

    /**
     * Create an associative array of the provenances objects indexed by their URI
     * @param type $provenances
     * @return array
     */
    private function mapProvenancesByUri($provenances) {
        $provenancesMap = [];
        if ($provenances !== null) {
            foreach ($provenances as $provenance) {
                $provenancesMap[$provenance->uri] = $provenance;
            }
        }

        return $provenancesMap;
    }

}
