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
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiConcernedItemModel extends WSActiveRecord {
    
    /**
     * uri of the item 
     *  (e.g http://www.phenome-fppn.fr/platform/2017/o1032588)
     * @var string 
     */
    public $uri;
    const URI = "uri";
    /**
     * uri of the rdf type of the item 
     *  (e.g http://www.phenome-fppn.fr/vocabulary/2017#Plot)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['rdfType', 'uri'], 'safe']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'uri' => 'URI',
            'rdfType' => Yii::t('app', 'Type')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the concerned item
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiConcernedItemModel::URI];
        $this->rdfType = $array[YiiConcernedItemModel::RDF_TYPE];
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
        
        return $attributesArray;
    }
}
