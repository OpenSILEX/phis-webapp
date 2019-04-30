<?php

//**********************************************************************************************
//                                       YiiDataFileModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 jan. 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSDataFileModel;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use \app\models\wsModels\WSConstants;
use Yii;

/**
 * The yii model for the data file. 
 * Implements a customized Active Record (WSActiveRecord, for the web services 
 * access)
 * @author vincent.migot
 */
class YiiDataFileModel extends WSActiveRecord {
    
    /**
     * the uri of the data file (e.g http://www.phenome-fppn.fr/platform/2017/i170000000000)
     * @var string 
     */
    public $uri;
    const URI = "uri";
    /**
     * the uri of the rdf type of the data file (e.g http://www.opensilex.org/vocabulary/oeso#HemisphericalImage)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the items concerned by the data file
     * @var mixed (array<YiiConcernModel> or string)
     * @see YiiConcernedItemModel
     */
    public $concernedItems;
    const CONCERNED_ITEMS = "concernedItems";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSImageModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSDataFileModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['uri', 'rdfType'], 'required'],  
          [['concernedItems'], 'safe']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'rdfType' => Yii::t('app', 'Type'),
            'concernedItems' => Yii::t('app', 'Concerned Items'),
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of an image
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[self::URI];
        $this->rdfType = $array[self::RDF_TYPE];
        
        foreach ($array[self::CONCERNED_ITEMS] as $concernedItemArray) {
            $concernedItem = new YiiConcernedItemModel();
            $concernedItem->arrayToAttributes($concernedItemArray);
            $this->concernedItems[] = $concernedItem;
        }
    }

    /**
     * Create an array representing the image metadata
     * Used for the web service for example
     * @warning Actually implemented only for the search fonctionnality
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $attributesArray[self::URI] = $this->uri;
        $attributesArray[self::RDF_TYPE] = $this->rdfType;
        if (is_array($this->concernedItems)) {
            $attributesArray[self::CONCERNED_ITEMS] = $this->concernedItems;
        }
        if ($this->page != null) {
            $attributesArray[WSConstants::PAGE] = $this->page;
        }
        if ($this->pageSize != null) {
            $attributesArray[WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        
        return $attributesArray;
    }
    
    /**
     * get the list of the images types defined in the ontology.
     * @return the list of rdf types of images
     */
    public function getRdfTypes($sessionToken) {
        $imageConceptUri = "http://www.opensilex.org/vocabulary/oeso#Image";
        
        $imagesTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $this->page = $i;
            
            $params = null;
            
            if ($this->pageSize !== null) {
                $params[WSConstants::PAGE_SIZE] = $this->pageSize; 
            }
            if ($this->page !== null) {
                $params[WSConstants::PAGE] = $this->page;
            }
            
            $wsUriModel = new WSUriModel();
            $requestRes = $wsUriModel->getDescendants($sessionToken, $imageConceptUri, $params);
            
            if (!is_string($requestRes)) {
                if (isset($requestRes[WSConstants::TOKEN_INVALID])) {
                    return "token";
                } else {
                    foreach ($requestRes[WSConstants::DATA] as $imageType) {
                        $imagesTypes[$imageType->uri] = explode("#", $imageType->uri)[1];
                    }
                }
            } else {
                return $requestRes;
            }
        }
        
        return $imagesTypes;
    }
}
