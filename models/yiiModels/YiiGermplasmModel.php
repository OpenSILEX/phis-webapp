<?php

//******************************************************************************
//                                       YiiGermplasmModel.php
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 08 2019
// Subject: The Yii model for the germplasms. Used with web services
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSGermplasmModel;
use app\models\wsModels\WSConstants;
use app\models\wsModels\WSUriModel;


class YiiGermplasmModel extends WSActiveRecord {
    public $file;
    
    public $germplasmLabel;
    const GERMPLASM_LABEL = "germplasmLabel";
    
    public $germplasmType;
    const GERMPLASM_TYPE = "germplasmType";
    
    public $uri;
    const URI = "uri";
    
    public $label;
    const LABEL = "label";
    
    public $genus;   
    const GENUS = "fromGenus";
    public $genusURI;
    
    public $species;
    const SPECIES = "fromSpecies";
    public $speciesEN;
    public $speciesFR;
    public $speciesLA;
    public $speciesURI;
    
    public $variety;
    const VARIETY = "fromVariety";
    public $varietyURI;
    
    public $accession;
    const ACCESSION = "fromAccession";
    public $accessionURI;
    
    const LOT_TYPE = "lotType";
    public $lotType; 
    const LOT = "plantMaterialLot";
    public $lot;    
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSGermplasmModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }   
    
    protected function arrayToAttributes($array) {
        $this->germplasmType = $array[YiiVectorModel::GERMPLASM_TYPE];
    }
    
    /**
     * Create an array representing the germplasm
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiGermplasmModel::URI] = $this->uri;
        $elementForWebService[YiiGermplasmModel::GERMPLASM_LABEL] = $this->germplasmLabel;
        $elementForWebService[YiiGermplasmModel::GERMPLASM_TYPE] = $this->germplasmType;
        $elementForWebService[YiiGermplasmModel::GENUS] = $this->genus;
        $elementForWebService[YiiGermplasmModel::SPECIES] = $this->speciesEN;
        $elementForWebService[YiiGermplasmModel::VARIETY] = $this->variety;
        $elementForWebService[YiiGermplasmModel::ACCESSION] = $this->accession;
        $elementForWebService[YiiGermplasmModel::LOT] = $this->lot;
        $elementForWebService[YiiGermplasmModel::LOT_TYPE] = $this->lotType;   
       
        return $elementForWebService;
    }
    

     /**
     * calls web service and return the list of object types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the germplasm types
     */
    public function getGermplasmTypes($sessionToken) {
        $germplasmConceptUri = "http://www.opensilex.org/vocabulary/oeso#Germplasm";
        $params = [];
        if ($this->pageSize !== null) {
           $params[WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $germplasmConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[WSConstants::TOKEN_INVALID])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * calls web service and return the list of object types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the germplasm types
     */
    public function getLotTypes($sessionToken) {
        $lotConceptUri = "http://www.opensilex.org/vocabulary/oeso#PlantMaterialLot";
        $params = [];
        if ($this->pageSize !== null) {
           $params[WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $lotConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[WSConstants::TOKEN_INVALID])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * calls web service and return the list of germplasm label and URI
     * @param $sessionToken
     * @param $germplasmLabel
     * @param $germplasmType
     * @param $fromGenus
     * @param $fromSpecies
     * @param $fromVariety
     * @param $fromAccession
     * @return array with germplasms URIs and Labels
     */
    public function getGermplasmURIAndLabelList($sessionToken, $germplasmLabel, $germplasmType, $fromGenus, $fromSpecies, $fromVariety, $fromAccession) {
        $this->germplasmLabel = $germplasmLabel;
        $this->germplasmType = $germplasmType;
        $this->genus = $fromGenus;
        $this->speciesEN = $fromSpecies;
        $this->variety = $fromVariety;
        $this->accession = $fromAccession;
        $germplasms = $this->find($sessionToken, $this->attributesToArray());
        $germplasmsToReturn = [];
        if ($germplasms !== null) {
            //1. get the URIs
            foreach($germplasms as $germplasm) {
                $germplasmsToReturn[$germplasm->uri] = $germplasm->label;
            }
            
            //2. if there are other pages, get the other genus
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextGermplasms = $this->getGermplasmURIAndLabelList($sessionToken, $germplasmLabel, $germplasmType, $fromGenus, $fromSpecies, $fromVariety, $fromAccession);
                
                $germplasmsToReturn = array_merge($germplasmsToReturn, $nextGermplasms);
            }
            
            return $germplasmsToReturn;
        }

    }
}