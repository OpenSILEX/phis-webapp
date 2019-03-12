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
     * Value
     * @example http://www.opensilex.org/phenome-fppn/id/pest/10ecffd9-d828-456c-8638-d0524567b8de
     * @var string 
     */
    public $value;
    const VALUE = "value";
    
    /**
     * Value Labels
     * @example Campagnol
     * @var string 
     */
    public $valueLabels;
    const VALUE_LABELS = "valueLabels";
    
    /**
     * RDF Type
     * @example http://www.opensilex.org/vocabulary/oeev#Pest
     * @var string 
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    
    /**
     * RDF Type Labels
     * @example Pest
     * @var string 
     */
    public $rdfTypeLabels;
    const RDF_TYPE_LABELS = "rdfTypeLabels";
    
    /**
     * Relation
     * @example http://www.opensilex.org/vocabulary/oeev#hasPest
     * @var string 
     */
    public $relation;
    const RELATION = "relation";
    
    /**
     * Relation labels
     * @example has pest
     * @var string 
     */
    public $relationLabels;
    const RELATION_LABELS = "relationLabels";
    
    /**
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [[
                self::VALUE, 
                self::VALUE_LABELS,
                self::RDF_TYPE,
                self::RDF_TYPE_LABELS,
                self::RELATION,
                self::RELATION_LABELS
                ], 'safe'
            ]
        ];
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            self::VALUE => Yii::t('app', 'Value'),
            self::VALUE_LABELS => Yii::t('app', 'Value Labels'),
            self::RDF_TYPE => Yii::t('app', 'Type'),
            self::RDF_TYPE_LABELS => Yii::t('app', 'Type Labels'),
            self::RELATION => Yii::t('app', 'Relation'),
            self::RELATION_LABELS => Yii::t('app', 'Relation Type Labels')
        ];
    }
    
    /**
     * Fill the attributes with the information of the given array
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
     * Create an array representing the property.
     * Used for the web service for example.
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $attributesArray = parent::attributesToArray();
        $attributesArray[self::RDF_TYPE] = $this->rdfType;
        $attributesArray[self::RELATION] = $this->relation;
        $attributesArray[self::VALUE] = $this->value;
        
        return $attributesArray;
    }
}
