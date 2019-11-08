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
    
    public $germplasmType;
    const GERMPLASM_TYPE = "germplasmType";
    const TYPE_LABEL = "Type";
    
    public $genus;
    
    public $species;
    
    public $variety;
    
    public $accession;
    
    public $lotType;
    
    public $lot;
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSGermplasmModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            self::TYPE => Yii::t('app', self::TYPE_LABEL)
        ];
    }
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
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
}