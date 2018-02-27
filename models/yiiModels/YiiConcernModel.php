<?php

//**********************************************************************************************
//                                       YiiConcernModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: the Yii model for the concerned elements.
//***********************************************************************************************

namespace app\models\yiiModels;
use Yii;

/**
 * the model for the concerned elements
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiConcernModel extends app\models\wsModels\WSActiveRecord {
    
    /**
     * the uri of the element 
     *  (e.g http://www.phenome-fppn.fr/platform/2017/o1032588)
     * @var string 
     */
    public $uri;
    const URI = "uri";
    /**
     * the uri of the rdf type of the element 
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
        $this->uri = $array[YiiConcernModel::URI];
        $this->rdfType = $array[YiiConcernModel::RDF_TYPE];
    }

    /**
     * Create an array representing the concerned element
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $toReturn[YiiConcernModel::URI] = $this->uri;
        $toReturn[YiiConcernModel::RDF_TYPE] = $this->rdfType;
        
        return $toReturn;
    }
}
