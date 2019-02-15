<?php

//******************************************************************************
//                            YiiConcernedItemModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 jan. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;

/**
 * Model for the concerned items
 * @update [Andréas Garcia] 15 Feb., 2019: add labels
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiConcernedItemModel extends WSActiveRecord {
    
    /**
     * uri of the item 
     * @example http://www.phenome-fppn.fr/platform/2017/o1032588
     * @var string 
     */
    public $uri;
    const URI = "uri";
    
    /**
     * uri of the rdf type of the item 
     * @example http://www.opensilex.org/vocabulary/oeso#Plot
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    
    /**
     * labels 
     * @example [Plot Lavalette, Parcelle Lavalette]
     * @var array
     */
    public $labels;
    const LABELS = "labels";
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['rdfType', 'uri', 'labels'], 'safe']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'uri' => 'URI',
            'rdfType' => Yii::t('app', 'Type'),
            'labels' => Yii::t('app', 'Labels')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the concerned item
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiConcernedItemModel::URI];
        $this->rdfType = $array[YiiConcernedItemModel::RDF_TYPE];
        $this->labels = $array[YiiConcernedItemModel::LABELS];
    }

    /**
     * Create an array representing the concerned item
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $attributesArray = parent::attributesToArray();
        $attributesArray[YiiConcernedItemModel::URI] = $this->uri;
        $attributesArray[YiiConcernedItemModel::RDF_TYPE] = $this->rdfType;
        $attributesArray[YiiConcernedItemModel::LABELS] = $this->labels;
        
        return $attributesArray;
    }
}
