<?php

//**********************************************************************************************
//                                       GermplasmController.php 
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 08 2019
// Subject: implements the CRUD actions for YiiGermplasmModel
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\yiiModels\YiiGermplasmModel;
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
     * the Genus column
     * @var GENUS
     */
    const GENUS = "Genus";

    /**
     * the species column
     * @var SPECIES
     */
    const SPECIES = "Species";

    /**
     * the variety column
     * @var VARIETY
     */
    const VARIETY = "Variety";
    
    /**
     * the Accession column
     * @var ACCESSION
     */
    const ACCESSION = "Accession";
    
    /**
     * the Lot column
     * @var LOT
     */
    const LOT = "Lot";
    
    /**
     * the LotType column
     * @var LOT_TYPE
     */
    const LOT_TYPE = "LotType";
 
    /**
     * generates the germplasm creation page
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
        
        $lotTypesList = $this->getPlantMaterialLotTypes();
        if ($lotTypesList === "token") {
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
            'lotTypesList' =>json_encode(array_values($lotTypesList), JSON_UNESCAPED_SLASHES),
            'selectedGermplasmType' => $selectedGermplasmType
        ]);
    }
    
    /**
     * Creates the germplasms
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
            $varieties = $this->getVarieties();
        } else if ($germplasmType === Yii::$app->params['PlantMaterialLot']) {
            $species = $this->getSpecies();
            $varieties = $this->getVarieties();       
            $accessions = $this->getAccessions();
        }
        
        $return = [
            "germplasmUris" => [],
            "messages" => []
        ];
        
        if (count($germplasms) > 0) {
              
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
                        $germplasmModel->accessionURI = array_search($germplasm[4], $accessions);
                    } else if ($germplasm[3] !== ""){
                        $germplasmModel->varietyURI = array_search($germplasm[3], $varieties);
                    } else {
                        $germplasmModel->speciesURI = array_search($germplasm[2], $species);
                    }
                    $germplasmModel->lot = $germplasm[6];
                    $germplasmModel->lotType = $this->getlotTypeCompleteUri($germplasm[5]);
                }
              
                $forWebService[] = $this->getArrayForWebServiceCreate($germplasmModel);
                $insertionResult = $germplasmModel->insert($sessionToken, $forWebService);

                if ($insertionResult->{\app\models\wsModels\WSConstants::METADATA}->status[0]->exception->type != "Error") {
                      $return["germplasmUris"][] = $insertionResult->{\app\models\wsModels\WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
                      $return["messages"][] = "germplasm saved";
                } else {
                      $return["germplasmUris"][] = null;
                      $return["messages"][] = $insertionResult->{\app\models\wsModels\WSConstants::METADATA}->status[0]->exception->details;
                }             
              
                
            }
            return json_encode($return, JSON_UNESCAPED_SLASHES); 
        }
        return true;
    }
    
    /**
     * Creates array with germplasm in WS expected format
     * @return array
     */
    private function getArrayForWebServiceCreate($germplasmModel) {        
        
        if ($germplasmModel->germplasmType === Yii::$app->params['Genus']) {
            $p["rdfType"] = $germplasmModel->germplasmType;
            $p["label"] = $germplasmModel->genus;
            $p["URI"] = $germplasmModel->genusURI;
            
        } else if ($germplasmModel->germplasmType === Yii::$app->params['Species']) {
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
            
        } else if ($germplasmModel->germplasmType === Yii::$app->params['Variety']) {
            $p["rdfType"] = $germplasmModel->germplasmType;
            $p["label"] = $germplasmModel->variety;
            $p["URI"] = $germplasmModel->varietyURI;
            $property["rdfType"] = Yii::$app->params['Species'];
            $property["relation"] = Yii::$app->params['fromSpecies'];
            $property["value"] = $germplasmModel->speciesURI;
            $p["properties"][] = $property;
            
        } else if ($germplasmModel->germplasmType === Yii::$app->params['Accession']) {
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
            
        } else if ($germplasmModel->germplasmType === Yii::$app->params['PlantMaterialLot']) {
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
            
        } else {
            $p = [];
        }       

        return $p;
    }    
    
    /**
     * Gets the germplasm types
     * @return array of germplasm types URIs and labels 
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
     * Gets the plantMaterialLot types
     * @return array of plantMaterialLot  
     */
    public function getPlantMaterialLotTypes() {
        $model = new YiiGermplasmModel();
        
        $lotTypes = [];
        $model->page = 0;
        $model->pageSize = Yii::$app->params['webServicePageSizeMax'];
        $lotTypesConcepts = $model->getLotTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        if ($lotTypesConcepts === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($lotTypesConcepts[WSConstants::DATA] as $lotType) {
                $lotTypes[$lotType->uri] = explode("#", $lotType->uri)[1];
            }
        }        
        return $lotTypes;
    }
    
    /**
     * Gets the germplasm types
     * @return array of germplasm types URIs and labels 
     */
    public function getLotTypesLabel() {
        $lotTypes = this.getPlantMaterialLotTypes();
        $lotTypesLabels = [];
        foreach ($lotTypes as $lotType) {
                $lotTypesLabels[] = explode("#", $lotType->uri)[1];
            }
        
    }
    
    /**
     * 
     * @param string $lotTypeLabel
     * @return string the complete lot type uri corresponding to the given 
     *                lot type
     *                e.g. http://www.opensilex.org/vocabulary/oeso#SeedLot
     */
    private function getLotTypeCompleteUri($lotTypeLabel) {
        $lotTypesList = $this->getPlantMaterialLotTypes();
        $lotTypeUri = array_search($lotTypeLabel, $lotTypesList);
        return $lotTypeUri;
    }
    
    
    /**
     * Gets the list of all genus
     * @return list of genus label 
     */
    public function getGenus() {
        $model = new YiiGermplasmModel();
        $genusList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Genus'], null, null, null, null);

        return $genusList;
    }
    
    /**
     * Gets the list of all species
     * @return list of species label 
     */
    public function getSpecies() {
        $model = new YiiGermplasmModel();
        $speciesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Species'], null, null, null, null);

        return $speciesList;
    }    
        
    /**
     * Gets the list of all varieties
     * @return list of varieties label 
     */
    public function getVarieties() {
        $model = new YiiGermplasmModel();
        $varietiesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN], null, Yii::$app->params['Variety'], null, null, null, null);

        return $varietiesList;
    }
    
    /**
     * Gets the list of all accessions
     * @return list of accessions label 
     */
    public function getAccessions() {
        $model = new YiiGermplasmModel();
        $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN], null, Yii::$app->params['Accession'], null, null, null, null);

        return $accessionsList;
    }  
    
    /**
     * Method used to update the list of species when a genus is selected
     * @return list of species label
     */
    public function actionGetSpecies() {
        $fromGenus = Yii::$app->request->post()["fromGenus"];
        $model = new YiiGermplasmModel();
        if ($fromGenus === "") {
            $genusURI = null;
        } else { 
            $genus = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromGenus, Yii::$app->params['Genus'], null, null, null, null);
            $genusURI = array_search($fromGenus, $genus);   
        }
        $speciesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Species'], $genusURI, null, null, null);
       //$speciesList = this.getSpecies($fromGenus);
        return json_encode(array_values($speciesList), JSON_UNESCAPED_SLASHES); 
    }
    
    /**
     * Method used to update the list of varieties when a genus or a species is selected
     * @return list of variety label 
     */
    public function actionGetVarieties() {
        $fromGenus = Yii::$app->request->post()["fromGenus"];
        $fromSpecies = Yii::$app->request->post()["fromSpecies"];
        $model = new YiiGermplasmModel();        
        
        if ($fromGenus !== null) {
            if ($fromGenus === "") {
            $genusURI = null;
            } else { 
                $genus = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromGenus, Yii::$app->params['Genus'], null, null, null, null);              
                $genusURI = array_search($fromGenus, $genus);
            }
            $varietiesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Variety'], $genusURI, null, null, null);
        }
        
        if ($fromSpecies !== null) {
            if ($fromSpecies === "") {
                $speciesURI = null;
            } else { 
                $species = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromSpecies, Yii::$app->params['Species'], null, null, null, null);              
                $speciesURI = array_search($fromSpecies, $species);
            }
            $varietiesList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Variety'], null, $speciesURI, null, null);
        }
        
        return json_encode(array_values($varietiesList), JSON_UNESCAPED_SLASHES); 
    }
    
    /**
     * Method used to update the list of varieties when a genus or a species is selected
     * @return list of accessions label 
     */
    public function actionGetAccessions() {
        $fromGenus = Yii::$app->request->post()["fromGenus"];
        $fromSpecies = Yii::$app->request->post()["fromSpecies"];
        $fromVariety = Yii::$app->request->post()["fromVariety"];
        
        $model = new YiiGermplasmModel();
        if ($fromGenus !== null) {
            if ($fromGenus === "") {
            $genusURI = null;
            } else { 
                $genus = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromGenus, Yii::$app->params['Genus'], null, null, null, null);              
                $genusURI = array_search($fromGenus, $genus);
            }
            $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Accession'], $genusURI, null, null, null);
        }
        
        if ($fromSpecies !== null) {
            if ($fromSpecies === "") {
                $speciesURI = null;
            } else { 
                $species = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromSpecies, Yii::$app->params['Species'], null, null, null, null);              
                $speciesURI = array_search($fromSpecies, $species);
            }
            $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Accession'], null, $speciesURI, null, null);
        }
        
        if ($fromVariety !== null) {
            if ($fromVariety === "") {
                $varietyURI = null;
            } else { 
                $variety = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],$fromVariety, Yii::$app->params['Variety'], null, null, null, null);              
                $varietyURI = array_search($fromVariety, $variety);
            }
            $accessionsList = $model->getGermplasmURIAndLabelList(Yii::$app->session[WSConstants::ACCESS_TOKEN],null, Yii::$app->params['Accession'], null, null, $varietyURI, null);
        }
        
        return json_encode(array_values($accessionsList), JSON_UNESCAPED_SLASHES); 
    }

}
