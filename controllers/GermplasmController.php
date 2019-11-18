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
use app\models\wsModels\WSConstants;
use yii\web\UploadedFile;

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
     * 
     * @param string $species
     * @return boolean true if the specie uri is in the species list
     */
    private function existSpecies($species) {
        $aoModel = new YiiScientificObjectModel();
        return in_array($species, $aoModel->getSpeciesUriList());
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
        //$sessionToken = Yii::$app->session['access_token'];
        $model = new YiiGermplasmModel();
        
        $germplasmTypes = $this->getGermplasmTypes();
        if ($germplasmTypes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $genusList = $this->getGenus();
        if ($genusList === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        $speciesList = $this->getSpecies();
        if ($speciesList === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $varietiesList = $this->getVarieties();
        if ($varietiesList === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $accessionsList = $this->getAccessions();
        if ($accessionsList === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $selectedGermplasmType = isset($_POST['germplasmType']) ? $_POST['germplasmType'] : null;

        return $this->render('create', [
            'model' => $model,
            'germplasmTypes' => $germplasmTypes,
            'genusList' => json_encode(array_values($genusList), JSON_UNESCAPED_SLASHES),
            'speciesList' => json_encode(array_values($speciesList), JSON_UNESCAPED_SLASHES),
            'varietiesList' => json_encode(array_values($varietiesList), JSON_UNESCAPED_SLASHES),
            'accessionsList' => json_encode(array_values($accessionsList), JSON_UNESCAPED_SLASHES),
            'selectedGermplasmType' => $selectedGermplasmType
        ]);
    }
    /**
     * Creates the given sensors
     * @return string of the creation JSON 
     */
    public function actionCreateMultipleGermplasm() {
        $germplasms = json_decode(Yii::$app->request->post()["germplasms"]);
        $germplasmType = Yii::$app->request->post()["germplasmType"];
        $sessionToken = Yii::$app->session['access_token'];
        
        if ($germplasmType === Yii::$app->params['Species']) {
            $genusList = $this->getGenus();
        } else if ($germplasmType === Yii::$app->params['Variety']) {
            $species = $this->getSpecies();
        } else if ($germplasmType === Yii::$app->params['Accession']) {
            $species = $this->getSpecies();
            $varieties = $this->getVarietiesFromSpecies();
        } else if ($germplasmType === Yii::$app->params['PlantMaterialLot']) {
            $species = $this->getSpecies();
            $varieties = $this->getVarietiesFromSpecies();       
            $accessions = $this->getAccessionFromSpecies();
        }
        
        if (count($germplasms) > 0) {
            $germplasmUris = null;
            foreach ($germplasms as $germplasm) {
                $forWebService = null;
                $germplasmModel = new YiiGermplasmModel();
                $germplasmModel->germplasmType = $germplasmType;
              
                if ($germplasmModel->germplasmType === Yii::$app->params['Genus']) {
                    $germplasmModel->genus = $germplasm[1];
                    if ($germplasm[2] !== "") {
                        $germplasmModel->genusURI = $germplasm[2];
                    }
                } else if ($germplasmModel->germplasmType === Yii::$app->params['Species']) {
                    if ($germplasm[1] !== "") {
                        $germplasmModel->genusURI = array_search($germplasm[1], $genusList);
                    }
                    if ($germplasm[2] !== "") {
                        $germplasmModel->speciesEN = $germplasm[2];
                    }
                    if ($germplasm[3] !== "") {
                        $germplasmModel->speciesFR = $germplasm[3];
                    }         
                    if ($germplasm[4] !== "") {
                        $germplasmModel->speciesLA = $germplasm[4];
                    }
                    if ($germplasm[5] !== "") {
                        $germplasmModel->speciesURI = $germplasm[5];
                    } 
                
                } else if ($germplasmModel->germplasmType === Yii::$app->params['Variety']) {
                    $germplasmModel->speciesURI = array_search($germplasm[2], $species);
                    $germplasmModel->variety = $germplasm[3];

                } else if ($germplasmModel->germplasmType === Yii::$app->params['Accession']) {
                    if ($germplasm[3] !== "") {
                        $germplasmModel->varietyURI = array_search($germplasm[3], $varieties);
                    } else {
                        $germplasmModel->speciesURI = array_search($germplasm[2], $species);
                    }
                    $germplasmModel->accession = $germplasm[4];
                    
                } else if ($germplasmModel->germplasmType === Yii::$app->params['PlantMaterialLot']) {
                    if ($germplasm[4] !== "") {
                        $germplasmModel->accession = array_search($germplasm[4], $accessions);
                    } else if ($germplasm[3] !== ""){
                        $germplasmModel->variety = array_search($germplasm[3], $varieties);
                    } else {
                        $germplasmModel->species = array_search($germplasm[2], $species);
                    }
                    $germplasmModel->lot = $germplasm[6];
                    $germplasmModel->lotType = $germplasm[5];
                }
              
              $forWebService[] = $this->getArrayForWebServiceCreate($germplasmModel);
              $insertionResult = $germplasmModel->insert($sessionToken, $forWebService);
              
              $germplasmUris[] = $insertionResult->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
            }
            return json_encode($germplasmUris, JSON_UNESCAPED_SLASHES); 
        }
        return true;
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
    private function getArrayForWebServiceCreate($germplasmModel) {        
        
        if ($germplasmModel->germplasmType === "http://www.opensilex.org/vocabulary/oeso#Genus") {
            $p["rdfType"] = $germplasmModel->germplasmType;
            $p["label"] = $germplasmModel->genus;
            $p["URI"] = $germplasmModel->genusURI;
            
        } else if ($germplasmModel->germplasmType === "http://www.opensilex.org/vocabulary/oeso#Species") {
            $p["rdfType"] = $germplasmModel->germplasmType;
            $p["label"] = $germplasmModel->speciesEN;
            $p["URI"] = $germplasmModel->speciesURI;
            if ($germplasmModel->genusURI !== false) {
                $genusProperty["rdfType"] = Yii::$app->params['Genus'];
                $genusProperty["relation"] = Yii::$app->params['fromGenus'];
                $genusProperty["value"] = $germplasmModel->genusURI;
                $p["properties"][] = $genusProperty; 
                $p["label"] = $germplasmModel->speciesEN;
            }
            if ($germplasmModel->speciesEN !== null) {
                $labelEN["relation"] = Yii::$app->params['rdfsLabel'];
                $labelEN["value"] = $germplasmModel->speciesEN .'@en';
                $p["properties"][] = $labelEN;  
            }
            if ($germplasmModel->speciesFR !== null) {
                $labelFR["relation"] = Yii::$app->params['rdfsLabel'];
                $labelFR["value"] = $germplasmModel->speciesFR . '@fr';
                $p["properties"][] = $labelFR;  
            }
            if ($germplasmModel->speciesLA !== null) {
                $labelLA["relation"] = Yii::$app->params['rdfsLabel'];
                $labelLA["value"] = $germplasmModel->speciesLA . '@la';
                $p["properties"][] = $labelLA;  
            }            
            
        } else if ($germplasmModel->germplasmType === "http://www.opensilex.org/vocabulary/oeso#Variety") {
            $p["rdfType"] = $germplasmModel->germplasmType;
            $p["label"] = $germplasmModel->variety;
            $p["URI"] = $germplasmModel->varietyURI;
            $property["rdfType"] = Yii::$app->params['Species'];
            $property["relation"] = Yii::$app->params['fromSpecies'];
            $property["value"] = $germplasmModel->speciesURI;
            $p["properties"][] = $property;
            
        } else if ($germplasmModel->germplasmType === "http://www.opensilex.org/vocabulary/oeso#Accession") {
            $p["rdfType"] = $germplasmModel->germplasmType;
            $p["label"] = $germplasmModel->accession;
            $p["URI"] = $germplasmModel->accessionURI;
            if ($germplasmModel->varietyURI !== null) {
                $property["rdfType"] = Yii::$app->params['Variety'];
                $property["relation"] = Yii::$app->params['fromVariety'];
                $property["value"] = $germplasmModel->varietyURI;
                $p["properties"][] = $property;
            } else {
                $property["rdfType"] = Yii::$app->params['Species'];
                $property["relation"] = Yii::$app->params['fromSpecies'];
                $property["value"] = $germplasmModel->speciesURI;
                $p["properties"][] = $property;
            }
            
        } else if ($germplasmModel->germplasmType === "http://www.opensilex.org/vocabulary/oeso#PlantMaterialLot") {
            $p["rdfType"] = $germplasmModel->lotType;
            $p["label"] = $germplasmModel->lot;
            $p["URI"] = $germplasmModel->accessionURI;
            if ($germplasmModel->accessionURI !== null) {
                $property["rdfType"] = Yii::$app->params['Accession'];
                $property["relation"] = Yii::$app->params['fromAccession'];
                $property["value"] = $germplasmModel->accessionURI;
                $p["properties"][] = $property;
            } else {
                if ($germplasmModel->varietyURI !== null) {
                $property["rdfType"] = Yii::$app->params['Variety'];
                $property["relation"] = Yii::$app->params['fromVariety'];
                $property["value"] = $germplasmModel->varietyURI;
                $p["properties"][] = $property;
                } else {
                    $property["rdfType"] = Yii::$app->params['Species'];
                    $property["relation"] = Yii::$app->params['fromSpecies'];
                    $property["value"] = $germplasmModel->speciesURI;
                    $p["properties"][] = $property;
                }
            }
            
        }                    

        return $p;
    }
    
    
    /**
     * Gets the germplasm types URIs.
     * @return germplasm types URIs 
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
    
    /**
     * Gets the germplasm types URIs.
     * @return germplasm types URIs 
     */
    public function getGenus() {
        $model = new YiiGermplasmModel();

        $model->page = 0;
        $model->pageSize = Yii::$app->params['webServicePageSizeMax'];
        $genusList = $model->getGenusURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN]);

        return $genusList;
    }
    
    /**
     * Gets the germplasm types URIs.
     * @return germplasm types URIs 
     */
    public function getSpecies() {
        $model = new YiiGermplasmModel();
        $speciesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Species", null, null, null, null);

        return $speciesList;
    }
    
        
    /**
     * Gets the germplasm types URIs.
     * @return germplasm types URIs 
     */
    public function getVarieties() {
        $model = new YiiGermplasmModel();
        $varietiesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN], null, "http://www.opensilex.org/vocabulary/oeso#Variety", null, null, null, null);

        return $varietiesList;
    }
    
    /**
     * Gets the germplasm types URIs.
     * @return germplasm types URIs 
     */
    public function getAccessions() {
        $model = new YiiGermplasmModel();
        $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],"http://www.opensilex.org/vocabulary/oeso#Variety", null, null, null, null);

        return $accessionsList;
    }
    

    
    public function actionGetSpecies() {
        $fromGenus = Yii::$app->request->post()["fromGenus"];
        $model = new YiiGermplasmModel();
        $genus = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromGenus, "http://www.opensilex.org/vocabulary/oeso#Genus", null, null, null, null);
        $genusURI = array_search($fromGenus, $genus);      
        $speciesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Species", $genusURI, null, null, null);
       //$speciesList = this.getSpecies($fromGenus);
        return json_encode(array_values($speciesList), JSON_UNESCAPED_SLASHES); 
    }
    
    public function actionGetVarieties() {
        $fromGenus = Yii::$app->request->post()["fromGenus"];
        $fromSpecies = Yii::$app->request->post()["fromSpecies"];
        $model = new YiiGermplasmModel();
        if ($fromGenus !== null) {
            $genus = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromGenus, "http://www.opensilex.org/vocabulary/oeso#Genus", null, null, null, null);              
            $genusURI = array_search($fromGenus, $genus);
            $varietiesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Variety", $genusURI, null, null, null);
        }
        
        if ($fromSpecies !== null) {
            $species = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromSpecies, "http://www.opensilex.org/vocabulary/oeso#Species", null, null, null, null);              
            $speciesURI = array_search($fromSpecies, $species);
            $varietiesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Variety", null, $speciesURI, null, null);
        }
        
        return json_encode(array_values($varietiesList), JSON_UNESCAPED_SLASHES); 
    }
    
    public function actionGetAccessions() {
        $fromGenus = Yii::$app->request->post()["fromGenus"];
        $fromSpecies = Yii::$app->request->post()["fromSpecies"];
        $fromVariety = Yii::$app->request->post()["fromVariety"];
        
        $model = new YiiGermplasmModel();
        if ($fromGenus !== null) {
            $genus = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromGenus, "http://www.opensilex.org/vocabulary/oeso#Genus", null, null, null, null);              
            $genusURI = array_search($fromGenus, $genus);
            $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Accession", $genusURI, null, null, null);
        }
        
        if ($fromSpecies !== null) {
            $species = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromSpecies, "http://www.opensilex.org/vocabulary/oeso#Species", null, null, null, null);              
            $speciesURI = array_search($fromSpecies, $species);
            $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Accession", null, $speciesURI, null, null);
        }
        
        if ($fromVariety !== null) {
            $variety = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromVariety, "http://www.opensilex.org/vocabulary/oeso#Variety", null, null, null, null);              
            $varietyURI = array_search($fromVariety, $variety);
            $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, "http://www.opensilex.org/vocabulary/oeso#Accession", null, null, $varietyURI, null);
        }
        
        return json_encode(array_values($accessionsList), JSON_UNESCAPED_SLASHES); 
    }
    
 
//    public function actionImportFile() {
//        $germplasmModel = new YiiGermplasmModel();
//        if ($germplasmModel->load(Yii::$app->request->post())) {
//            $data[] = null;
//            $selectedGermplasmType = isset($_POST['germplasmType']) ? $_POST['germplasmType'] : null;
//            //Store uploaded CSV file
//            $document = UploadedFile::getInstance($germplasmModel, 'file');
//            $serverFilePath = \config::path()['documentsUrl'] . "GermplasmFiles/" . $document->name;
//            $document->saveAs($serverFilePath);
//                //Read CSV file content
//            $fileContent = str_getcsv(file_get_contents($serverFilePath), "\n");
//            $csvHeaders = str_getcsv(array_shift($fileContent), Yii::$app->params['csvSeparator']);
//            unlink($serverFilePath);
//
//            if ($selectedGermplasmType === Yii::$app->params['Species']) {
//                foreach ($fileContent as $rowStr) {
//                    $row = str_getcsv($rowStr, Yii::$app->params['csvSeparator']);
//                    $data[] = $row;
//                }
//            }
//
//            return $this->render('_form', [
//                'model'=> $germplasmModel,
//                'germplasmType' => $selectedGermplasmType,
//                'data' => $data
//            ]);
//        } else {
//            return $this->render('create', [
//                        'model' => $germplasmModel,
//            ]);
//        }
//    }

}
