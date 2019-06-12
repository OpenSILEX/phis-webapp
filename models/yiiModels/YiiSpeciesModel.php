<?php

//******************************************************************************
//                                       YiiSpeciesModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 déc. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use Yii;

/**
 * The yii model for the species.
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSSpeciesModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiSpeciesModel extends WSActiveRecord {
    
    /**
     * The uri of the species.
     * @example http://www.phenome-fppn.fr/id/species/zeamays
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * The label of the species.
     * @example Maize
     * @var string
     */
    public $label;
    const LABEL = "label";
    
    public $language;
    const LANGUAGE = "language";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSSpeciesModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new \app\models\wsModels\WSSpeciesModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['uri', 'label'], 'required'],
          [['uri', 'label'], 'string', 'max' => 200]
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
          'uri' => Yii::t('app', 'URI'),
          'label' => Yii::t('app', 'Label')
        ];
    }
    
    /**
     * Allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a species
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiSpeciesModel::URI];
        $this->label = $array[YiiSpeciesModel::LABEL];
    }
    
    /**
     * Create an array representing the species.
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiSpeciesModel::URI] = $this->uri;
        $elementForWebService[YiiSpeciesModel::LABEL] = $this->label;
        $elementForWebService[YiiSpeciesModel::LANGUAGE] = $this->language;
        
        return $elementForWebService;
    }
    
    /**
     * Get the list of uri of the species.
     * @param string $sessionToken
     * @return Array
     * @example [
     *      "http://www.opensilex.org/id/species/betavulgaris", 
     *      "http://www.opensilex.org/id/species/brassicanapus"
     * ]
     */
    public function getSpeciesUriList($sessionToken) {
        $species = $this->find($sessionToken, $this->attributesToArray());
        $speciesToReturn = [];
        
        if ($species !== null) {
            //1. get the URIs
            foreach($species as $specie) {
                $speciesToReturn[] = $specie->uri;
            }
            
            //2. if there are other pages, get the other species
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextSpecies = $this->getSpeciesList($sessionToken);
                
                $speciesToReturn = array_merge($speciesToReturn, $nextSpecies);
            }
            
            return $speciesToReturn;
        }
    }
    
    public function getSpeciesUriLabelList($sessionToken) {
        $this->pageSize = 500;
        $species = $this->find($sessionToken, $this->attributesToArray());
        $speciesToReturn = [];
        
        if ($species !== null) {
            //1. get the URIs
            foreach($species as $specie) {
                $speciesToReturn[$specie->uri] = $specie->label;
            }
            
            //2. if there are other pages, get the other species
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextSpecies = $this->getSpeciesUriLabelList($sessionToken);
                
                $speciesToReturn = array_merge($speciesToReturn, $nextSpecies);
            }
            
            return $speciesToReturn;
        }
    }
}