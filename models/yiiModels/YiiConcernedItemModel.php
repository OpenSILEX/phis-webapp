<?php

//******************************************************************************
//                            YiiConcernedItemModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 Jan. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;

/**
 * Model for the concerned items.
 * @update [Andréas Garcia] 15 Feb. 2019: add labels.
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiConcernedItemModel extends WSActiveRecord {
    
    /**
     * URI of the item.
     * @example http://www.phenome-fppn.fr/platform/2017/o1032588
     * @var string 
     */
    public $uri;
    const URI = "uri";
    const URI_LABEL = "URI";
    
    /**
     * URI of the RDF type of the item.
     * @example http://www.opensilex.org/vocabulary/oeso#Plot
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    const RDF_TYPE_LABEL = "Type";
    
    /**
     * Labels.
     * @example [Plot Lavalette, Parcelle Lavalette]
     * @var array
     */
    public $labels;
    const LABELS = "labels";
    const LABELS_LABEL = "Labels";
    
    /**
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [[self::RDF_TYPE, self::URI, self::LABELS], 'safe']
        ];
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            self::URI => self::URI_LABEL,
            self::RDF_TYPE => Yii::t('app', self::RDF_TYPE_LABEL),
            self::LABELS => Yii::t('app', self::LABELS_LABEL)
        ];
    }
    
    /**
     * Allow to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the concerned item
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[self::URI];
        $this->rdfType = $array[self::RDF_TYPE];
        $this->labels = $array[self::LABELS];
    }

    /**
     * Create an array representing the concerned item.
     * Used for the web service for example.
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $attributesArray = parent::attributesToArray();
        $attributesArray[self::URI] = $this->uri;
        $attributesArray[self::RDF_TYPE] = $this->rdfType;
        $attributesArray[self::LABELS] = $this->labels;
        
        return $attributesArray;
    }
}
