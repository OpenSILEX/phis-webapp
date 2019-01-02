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

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSEventModel;

use Yii;

/**
 * The yii model for the events. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSUriModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class YiiEventModel extends WSActiveRecord {
    
    /**
     * @example http://www.phenome-fppn.fr/diaphen/s18001
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * @example http://www.phenome-fppn.fr/vocabulary/2018/oeev#MoveFrom
     * @var string
     */
    public $type;
    const TYPE = "type";
    /**
     * @example Skye Instruments
     * @var string
     */
    public $concernsItems; 
    const CONCERNS_ITEMS = "concernsItems";
    /**
     * @example E1JFHS849DNSKF8DH
     * @var string 
     */
    public $dateTimeString;
    const DATETIME_STRING = "dateTimeString";
    
    public $properties;
    const PROPERTIES = "properties";
    const RELATION = "relation";
    const VALUE = "value";
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSEventModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize 
                : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page 
                : $this->page = null;
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
       return [ 
           [['uri'], 'required']
           , [['type', 'concernsItems', 'dateTimeString', 'documents','properties'], 'safe']
        ]; 
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-structure-models.html#attribute-labels
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'uri' => 'URI'
            , 'type' => Yii::t('app', 'Type')
            , 'concernsItems' => Yii::t('app', 'Concerned Elements')
            , 'dateTimeString' => Yii::t('app', 'Date')
            , 'properties' => Yii::t('app', 'Properties')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a event
     */
    protected function arrayToAttributes($array) {
        var_dump($array);
        $this->uri = $array[YiiEventModel::URI];
        $this->type = $array[YiiEventModel::TYPE];
        if ($array[YiiEventModel::CONCERNS_ITEMS]) {
            $this->concernsItems 
                    = get_object_vars($array[YiiEventModel::CONCERNS_ITEMS]);
        } 
        $this->dateTimeString = $array[YiiEventModel::DATETIME_STRING];
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
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] 
                   = $this->pageSize; 
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
