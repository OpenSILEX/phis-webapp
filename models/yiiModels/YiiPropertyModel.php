<?php

//******************************************************************************
//                            YiiPropertyModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 15 Feb., 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;

/**
 * Model for properties
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class YiiPropertyModel extends WSActiveRecord {
    
    /**
     * value
     * @var string 
     */
    public $value;
    const VALUE = "value";
    
    /**
     * value Labels
     * @var string 
     */
    public $valueLabels;
    const VALUE_LABELS = "valueLabels";
    
    /**
     * rdf Type
     * @var string 
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    
    /**
     * rdf Type Labels
     * @var string 
     */
    public $rdfTypeLabels;
    const RDF_TYPE_LABELS = "rdfTypeLabels";
    
    /**
     * relation
     * @var string 
     */
    public $relation;
    const RELATION = "relation";
    
    /**
     * relationLabels
     * @var string 
     */
    public $relationLabels;
    const RELATION_LABELS = "relationLabels";
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [[
                YiiPropertyModel::VALUE, 
                YiiPropertyModel::VALUE_LABELS,
                YiiPropertyModel::RDF_TYPE,
                YiiPropertyModel::RDF_TYPE_LABELS,
                YiiPropertyModel::RELATION,
                YiiPropertyModel::RELATION_LABELS
                ], 'safe']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            YiiPropertyModel::VALUE => Yii::t('app', 'Value'),
            YiiPropertyModel::VALUE_LABELS => Yii::t('app', 'Value Labels'),
            YiiPropertyModel::RDF_TYPE => Yii::t('app', 'Type'),
            YiiPropertyModel::RDF_TYPE_LABELS => Yii::t('app', 'Type Labels'),
            YiiPropertyModel::RELATION => Yii::t('app', 'Relation'),
            YiiPropertyModel::RELATION_LABELS => Yii::t('app', 'Relation Type Labels')
        ];
    }
    
    /**
     * fills the attributes with the information of the given array
     * @param array $array array key => value
     */
    protected function arrayToAttributes($array) {
        $this->value = $array->value;
        if(isset($array->valueLabels)){
            $this->valueLabels = $array->valueLabels;
        }
        $this->rdfType = $array->rdfType;
        if(isset($array->rdfTypeLabels)){
            $this->rdfTypeLabels = $array->rdfTypeLabels;
        }
        $this->relation = $array->relation;
        if(isset($array->relationLabels)){
            $this->relationLabels = $array->relationLabels;
        }   
    }

    /**
     * Create an array representing the concerned item
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $attributesArray = parent::attributesToArray();
        $attributesArray[YiiPropertyModel::RDF_TYPE] = $this->rdfType;
        $attributesArray[YiiPropertyModel::LABELS] = $this->labels;
        
        return $attributesArray;
    }
}
