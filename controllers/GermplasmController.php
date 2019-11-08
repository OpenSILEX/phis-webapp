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
use app\models\yiiModels\YiiGermplasmModel;
use app\models\yiiModels\ScientificObjectSearch;
use app\models\yiiModels\YiiExperimentModel;
use app\models\wsModels\WSConstants;

require_once '../config/config.php';

/**
 * CRUD actions for YiiScientificObjectModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiScientificObjectModel
 * @update [Bonnefont Julien] 12 Septembre, 2019: add visualization functionnalities & cart & cart action to add Event on multipe scientific objects
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class GermplasmController extends Controller {
    
    const GERMPLASM_TYPES = "germplasmTypes";
    
    /**
     * the Genus column for the csv files
     * @var GENUS
     */
    const GENUS = "Genus";


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
     * the Accession column for the csv files
     * @var ACCESSION
     */
    const ACCESSION = "Accession";
    
    /**
     * the Lot column for the csv files
     * @var LOT
     */
    const LOT = "Lot";
    
    /**
     * the LotType column for the csv files
     * @var LOT_TYPE
     */
    const LOT_TYPE = "LotType";


    

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
//    public function getObjectTypes() {
//        $model = new YiiScientificObjectModel();
//
//        $objectsTypes = [];
//        $totalPages = 1;
//        for ($i = 0; $i < $totalPages; $i++) {
//            $model->page = $i;
//            $scientificObjectConcepts = $model->getObjectTypes(Yii::$app->session['access_token']);
//            if ($scientificObjectConcepts === "token") {
//                return "token";
//            } else {
//                $totalPages = $scientificObjectConcepts[\app\models\wsModels\WSConstants::PAGINATION][\app\models\wsModels\WSConstants::TOTAL_PAGES];
//
//                foreach ($scientificObjectConcepts[\app\models\wsModels\WSConstants::DATA] as $objectType) {
//                    $objectsTypes[] = explode("#", $objectType->uri)[1];
//                }
//            }
//        }
//
//        return $objectsTypes;
//    }

//    /**
//     * get the species 
//     * @return array list of species
//     */
//    public function getSpecies() {
//        $speciesModel = new \app\models\yiiModels\YiiSpeciesModel();
//        return $speciesModel->getSpeciesUriLabelList(Yii::$app->session['access_token']);
//    }

//    /**
//     * get the csv file header
//     * @return array list of the columns names for a scientific objects file
//     */
//    private function getHeaderList() {
//        return [ScientificObjectController::ALIAS, ScientificObjectController::RDF_TYPE,
//            ScientificObjectController::EXPERIMENT_URI, ScientificObjectController::GEOMETRY,
//            ScientificObjectController::SPECIES, ScientificObjectController::VARIETY,
//            ScientificObjectController::EXPERIMENT_MODALITIES, ScientificObjectController::REPLICATION];
//    }

//    /**
//     * 
//     * @param array $csvHeader an array with for example the 
//     *                         columns of a csv file
//     * @return boolean true if the required columns are in the $csvHeader 
//     *                 false if not
//     */
//    private function existRequiredColumns($csvHeader) {
//        return in_array(ScientificObjectController::ALIAS, $csvHeader) && in_array(ScientificObjectController::RDF_TYPE, $csvHeader) && in_array(ScientificObjectController::EXPERIMENT_URI, $csvHeader);
//    }

    /**
     * check if the columns names exist in the file. 
     * @param array $csvHeader the header columns list 
     * @return array if error the errors in the file 
     *               else a key value array corresponding to the columns number 
     *                    and their names in the file. 
     *                    e.g : "alias" => 3. The column alias is the third 
     *                          column in the csv file
     */
//    private function getCSVHeaderCorrespondancesOrErrors($csvHeader) {
//        $headersNamesNumber = null;
//        $headersNames = $this->getHeaderList();
//        if ($this->existRequiredColumns($csvHeader)) {
//            foreach ($headersNames as $headerName) {
//                $keyNumer = array_search($headerName, $csvHeader);
//                if (is_int($keyNumer)) {
//                    $headersNamesNumber[$headerName] = $keyNumer;
//                }
//            }
//        } else {
//            $headersNamesNumber["Error"][] = Yii::t('app/messages', 'Required column missing');
//        }
//
//        return $headersNamesNumber;
//    }

    /**
     * 
     * @param array $array
     * @return array the array without values equals to ""
     */
//    private function getArrayWithoutEmptyValues($array) {
//        $toReturn = null;
//        foreach ($array as $element) {
//            if ($element != "") {
//                $toReturn[] = $element;
//            }
//        }
//
//        return $toReturn;
//    }

    

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
//    private function getCSVErrors($csvContent) {
//        //SILEX:todo
//        //create a library for the data type check (isGeometry, isDate, ...)
//        //\SILEX:todo
//        //1. check header
//        $headerCheck = $this->getCSVHeaderCorrespondancesOrErrors(str_getcsv($csvContent[0], Yii::$app->params['csvSeparator']));
//        $errors = null;
//        if (isset($headerCheck["Error"])) {
//            $errors["header"] = $headerCheck["Error"];
//        } else { //2. check each cell's content
//            $experiments = [];
//            for ($i = 1; $i < count($csvContent); $i++) {
//                $row = str_getcsv($csvContent[$i], Yii::$app->params['csvSeparator']);
//                If ($row[$headerCheck["Geometry"]] != "") {
//                    if (!$this->isGeometryOk($row[$headerCheck["Geometry"]])) {
//                        $error = null;
//                        $error["line"] = "L." . ($i + 1);
//                        $error["column"] = ScientificObjectController::GEOMETRY;
//                        $error["message"] = Yii::t('app/messages', 'Bad geometry given') . ". " . Yii::t('app/messages', 'Expected format') . " : POLYGON ((1.33 2.33, 3.44 5.66, 4.55 5.66, 6.77 7.88, 1.33 2.33))";
//                        $errors[] = $error;
//                    }
//                }
//                if (!in_array($row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]], $experiments)) {
//                    if (!$this->existExperiment($row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]])) {
//                        $error = null;
//                        $error["line"] = "L." . ($i + 1);
//                        $error["column"] = ScientificObjectController::EXPERIMENT_URI;
//                        $error["message"] = Yii::t('app/messages', 'Unknown experiment') . " : " . $row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]];
//                        $errors[] = $error;
//                    }
//                    $experiments[] = $row[$headerCheck[ScientificObjectController::EXPERIMENT_URI]];
//                }
//                if (!$this->existSpecies($row[$headerCheck["Species"]])) {
//                    $error = null;
//                    $error["line"] = "L." . ($i + 1);
//                    $error["column"] = ScientificObjectController::SPECIES;
//                    $error["message"] = Yii::t('app/messages', 'Unknown species') . " : " . $row[$headerCheck[ScientificObjectController::SPECIES]];
//                    $errors[] = $error;
//                }
//                if ($row[$headerCheck["Alias"]] == "") {
//                    $error = null;
//                    $error["line"] = "L." . ($i + 1);
//                    $error["column"] = ScientificObjectController::ALIAS;
//                    $error["message"] = Yii::t('app/messages', 'Alias is missing');
//                    $errors[] = $error;
//                }
//            }
//        }
//
//        return $errors;
//    }

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
        //$sessionToken = Yii::$app->session['access_token'];
        $germplasmModel = new YiiGermplasmModel();
        
        $germplasmTypes = $this->getGermplasmTypes();
        $this->view->params['listGermplasmTypes'] = $germplasmTypes;

        return $this->render('create', [
                    'model' => $germplasmModel,
                    //'germplasmTypes' => json_encode($germplasmTypes, JSON_UNESCAPED_SLASHES)
        ]);
    }

    /**
     * create the given objects
     * @return string the json of the creation return
     */
    public function actionCreateMultipleScientificObjects() {
        $germplasms = json_decode(Yii::$app->request->post()["germplasm"]);
        $sessionToken = Yii::$app->session['access_token'];

        $return = [
            "objectUris" => [],
            "messages" => []
        ];

        $species = $this->getSpecies();

        if (count($germplasms) > 0) {
            $germplasmsToInsert = count($germplasms);
            $cpt = 0;
            $forWebService = [];
            foreach ($germplasms as $object) {
                $germplasmModel = new \app\models\yiiModels\YiiGermplasmModel();

                $germplasmModel->genus = $object[1];
                $germplasmModel->species = $this->getObjectTypeCompleteUri($object[2]);
                $germplasmModel->variety = array_search($object[3], $experiments);
                $germplasmModel->accession = $object[4];
                $germplasmModel->lot = $object[5];
                $germplasmModel->lotType = array_search($object[6], $species);


                $germplasm = $germplasmModel->attributesToArray();

                $forWebService[] = $this->getArrayForWebServiceCreate($germplasm);
                $cpt++;
                //Insert the scientific objects by 200
                if ($cpt === 200 || $cpt === $germplasmsToInsert) {
                    $objectsToInsert = $objectsToInsert - $cpt;
                    $cpt = 0;

                    $insertionResult = $germplasmModel->insert($sessionToken, $forWebService);
                  
                    $forWebService = [];
                    
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
            if (preg_match("/". $objectType . "\b/", $objectTypeUri)) {
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
     * Function to select all the filtered sci. obj. (URI & name)
     * @param type $uri
     * @param type $label
     * @param type $type
     * @param type $experiment
     * @param type $token
     * @return associative array of uri => label
     * 
     */
    public function getObjectList($uri, $label, $type, $experiment, $token) {

        $searchModel = new ScientificObjectSearch();
        $searchModel->uri = isset($uri) ? $uri : null;
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
                $items[$model->uri] = $model->label;
            }

            $totalPage = intval($searchModel->totalPages);
        }
        return $items;
    }

    /**
     * Ajax call from index view : an sci. obj. or all sci. obj. from the page are add to the cart (session variable)
     * @return the count of the cart
     */
    public function actionAddToCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        $itemsWithName = Yii::$app->request->post()["scientific-object"];
        if (isset($session['scientific-object'])) {
            $temp = $session['scientific-object'];

            foreach ($itemsWithName as $uri => $name) {
                $temp[$uri] = $name;
            }
            $session['scientific-object'] = $temp;
        } else {
            $session['scientific-object'] = $itemsWithName;
        }

        return ['totalCount' => count($session['scientific-object'])];
    }

    /**
     * Ajax call from index view : an sci. obj. or all sci. obj. from the page are removed from the cart (session variable)
     * @return the count of the cart
     */
    public function actionRemoveFromCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        if (Yii::$app->request->post()["scientific-object"]) {
            $cart = $session['scientific-object'];
            $itemsWithName = Yii::$app->request->post()["scientific-object"];
            $cart = array_diff_assoc($cart, $itemsWithName);
            $session['scientific-object'] = $cart;

            return ['totalCount' => count($session['scientific-object'])];
        }
    }

    /**
     * Ajax call from index view : all sci. obj.  are add to the cart (session variable)
     * @return the count of the cart
     */
    public function actionAllToAddToCart() {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;

        $objects = $this->getObjectList(Yii::$app->request->post()["uri"], Yii::$app->request->post()["alias"], Yii::$app->request->post()["type"], Yii::$app->request->post()["experiment"], Yii::$app->session['access_token']);
        $session['all-scientific-object'] = $objects;
        if ($objects) {
            if (isset($session['scientific-object'])) {
                $cart = $session['scientific-object'];
                foreach ($objects as $uri => $name) {
                    $cart[$uri] = $name;
                }
                $session['scientific-object'] = $cart;
            } else {
                $session['scientific-object'] = $objects;
            }
        }

        return ['totalCount' => count($session['scientific-object'])];
    }

    /**
     * Ajax call from index view : all sci. obj.  are removed from the cart (session variable)
     * @return the count of the cart
     * 
     */
    public function actionAllToRemoveFromCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;

        $allobjects = $session['all-scientific-object'];
        $cart = $session['scientific-object'];
        $cart = array_diff_assoc($cart, $allobjects);
        $session['scientific-object'] = $cart;

        return ['totalCount' => count($session['scientific-object'])];
    }

    /**
     * Ajax call from index view : delete the content of the cart 
     * @return the count of the cart
     * 
     */
    public function actionCleanCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        unset($session['scientific-object']);

        return ['totalCount' => 0];
    }

    /**
     * Ajax call from index view : get the content of the cart
     * @return array the content of the cart
     * 
     */
    public function actionGetCart() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $session = Yii::$app->session;
        return ['items' => $session['scientific-object']];
    }

    /**
     * scientific objects index (list of scientific objects)
     * @return mixed
     */
    public function actionIndex() {

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
                        'total' => count($session['scientific-object']),
                        'cart' => $session['scientific-object'],
                        'searchParams' => $searchParams,
            ]);
        }
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

        $stringToWrite = "ScientificObjectURI" . Yii::$app->params['csvSeparator'] .
                "Alias" . Yii::$app->params['csvSeparator'] .
                "RdfType" . Yii::$app->params['csvSeparator'] .
                "ExperimentURI" . Yii::$app->params['csvSeparator'] .
                "Geometry" .
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
 
                $stringToWrite .= $model->uri . Yii::$app->params['csvSeparator'] . 
                                 $model->label . Yii::$app->params['csvSeparator'] .
                                 $model->rdfType . Yii::$app->params['csvSeparator'] .
                                 $model->experiment . Yii::$app->params['csvSeparator'] . 
                                 '"' . $wktGeometry . '"' . Yii::$app->params['csvSeparator'] . 
                                 "\n";
            }

            $totalPage = intval($searchModel->totalPages);
        }
        file_put_contents($serverFilePath, $stringToWrite, FILE_APPEND);
        Yii::$app->response->sendFile($serverFilePath);
    }

    
    
    /**
     * Gets the germplasm types URIs.
     * @return event types URIs 
     */
    public function getGermplasmTypes() {
        $model = new YiiGermplasmModel();
        
        $germplasmTypes = [];
        $model->page = 0;
        $model->pageSize = Yii::$app->params['webServicePageSizeMax'];
        $germplasmTypesConcepts = $model->getGermplasmTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        if ($germplasmTypesConcepts === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($germplasmTypesConcepts[WSConstants::DATA] as $germplasmType) {
                $germplasmTypes[$germplasmType->uri] = $germplasmType->uri;
            }
        }
        
        return $germplasmTypes;
    }

}
