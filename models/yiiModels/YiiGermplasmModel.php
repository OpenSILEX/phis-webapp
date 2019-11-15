<?php

//******************************************************************************
//                                       YiiGermplasmModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: The Yii model for the sensors. Used with web services
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSGermplasmModel;
use app\models\wsModels\WSConstants;
use app\models\wsModels\WSUriModel;


class YiiGermplasmModel extends WSActiveRecord {
    public $file;
    
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
    
//    /**
//     * @return array the labels of the attributes
//     */
//    public function attributeLabels() {
//        return [
//            'germplasmType' => Yii::t('app', 'Type'),
//            'label' => Yii::t('app', 'Alias'),
//            'brand' => Yii::t('app', 'Brand'),
//            'serialNumber' => Yii::t('app', 'Serial Number'),
//            'inServiceDate' => Yii::t('app', 'In Service Date'),
//            'dateOfPurchase' => Yii::t('app', 'Date Of Purchase'),
//            'personInCharge' => Yii::t('app', 'Person In Charge')
//        ];
//    }
    
    
    protected function arrayToAttributes($array) {
        $this->germplasmType = $array[YiiVectorModel::GERMPLASM_TYPE];
    }
    
    /**
     * Create an array representing the experiment
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiGermplasmModel::URI] = $this->uri;
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
     * @return list of the sensors types
     */
    public function getGermplasmTypes($sessionToken) {
        $germplasmConceptUri = "http://www.opensilex.org/vocabulary/oeso#Germplasm";
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $germplasmConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
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
     * @return list of the sensors types
     */
    public function getGenusURIAndLabelList($sessionToken) {
        $this->germplasmType = "http://www.opensilex.org/vocabulary/oeso#Genus";
        $genusList = $this->find($sessionToken, $this->attributesToArray());
        $genusToReturn = [];
        
        if ($genusList !== null) {
            //1. get the URIs
            foreach($genusList as $genus) {
                $genusToReturn[$genus->uri] = $genus->genus;
            }
            
            //2. if there are other pages, get the other genus
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextGenus = $this->getGenusURIAndLabelList($sessionToken);
                
                $genusToReturn = array_merge($genusToReturn, $nextGenus);
            }
            
            return $genusToReturn;
        }

    }
    
        /**
     * calls web service and return the list of object types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getSpeciesURIAndLabelList($sessionToken) {
        $this->germplasmType = "http://www.opensilex.org/vocabulary/oeso#Species";
        $speciesList = $this->find($sessionToken, $this->attributesToArray());
        $speciesToReturn = [];
        if ($speciesList !== null) {
            //1. get the URIs
            foreach($speciesList as $species) {
                $speciesToReturn[$species->uri] = $species->species;
            }
            
            //2. if there are other pages, get the other genus
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextSpecies = $this->getSpeciesURIAndLabelList($sessionToken);
                
                $speciesToReturn = array_merge($speciesToReturn, $nextSpecies);
            }
            
            return $speciesToReturn;
        }

    }
    
            /**
     * calls web service and return the list of object types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getGermplasmURIAndLabelList($sessionToken, $germplasmType, $fromGenus, $fromSpecies, $fromVariety, $fromAccession) {
        $this->germplasmType = $germplasmType;
        $this->genus = $fromGenus;
        $this->species = $fromSpecies;
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
                $nextGermplasms = $this->getGermplasmURIAndLabelList($sessionToken, $germplasmType, $fromGenus, $fromSpecies, $fromVariety, $fromAccession);
                
                $germplasmsToReturn = array_merge($germplasmsToReturn, $nextGermplasms);
            }
            
            return $germplasmsToReturn;
        }

    }
}