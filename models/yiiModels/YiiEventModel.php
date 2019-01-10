<?php

//******************************************************************************
//                                       YiiEventModel.php
//
// Author(s): Andréas Garcia <andreas.garcia@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation dateTimeString: 02 janvier 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSEventModel;

/**
 * The yii model for an  Event. 
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSUriModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class YiiEventModel extends WSActiveRecord {
    
    /**
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * @var string
     */
    public $type;
    const TYPE = "type";
    /**
     * @var string
     */
    public $concerns; 
    const CONCERNS = "concerns";
    /**
     * @var string 
     */
    public $date;
    const DATE = "date";
    
    public $properties;
    const PROPERTIES = "properties";
    const RELATION = "relation";
    const VALUE = "value";
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSEventModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize 
                : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
       return [ 
           [[YiiEventModel::URI], 'required']
            , [[YiiEventModel::TYPE, 
                YiiEventModel::CONCERNS, 
                YiiEventModel::DATE, 
                YiiEventModel::PROPERTIES
            ] , 'safe']
        ]; 
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-structure-models.html#attribute-labels
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            YiiEventModel::URI => 'URI'
            , YiiEventModel::TYPE => Yii::t('app', 'Type')
            , YiiEventModel::PROPERTIES => Yii::t('app', 'Properties')
            , YiiEventModel::DATE => Yii::t('app', 'Date')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a event
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiEventModel::URI];
        $this->type = $array[YiiEventModel::TYPE];
        if ($array[YiiEventModel::CONCERNS]) {
            $this->concerns 
                    = get_object_vars($array[YiiEventModel::CONCERNS]);
        } 
        $this->date = $array[YiiEventModel::DATE];
        $this->properties = $array[YiiEventModel::PROPERTIES];
    }
    
    /**
     * allows to fill the property attribute with the information of the given 
     * array
     * @param array $array array key => value with the properties of a event 
     */
    protected function propertiesArrayToAttributes($array) {
        if ($array[YiiEventModel::PROPERTIES] !== null) {
            foreach ($array[YiiEventModel::PROPERTIES] as $property) {
                $propertyToAdd = null;
                $propertyToAdd[YiiEventModel::RELATION] = $property->relation; 
                $propertyToAdd[YiiEventModel::VALUE] = $property->value;
                $propertyToAdd[YiiEventModel::TYPE] = $property->type;
                $this->properties[] = $property;
            }
        }
    }

    /**
     * calls web service and returns the list of events types
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the events types
     */
    public function getEventsTypes($sessionToken) {
        $eventConceptUri 
                = "http://www.phenome-fppn.fr/vocabulary/2018/oeev#Event";
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel
                ->getDescendants($sessionToken, $eventConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
}
