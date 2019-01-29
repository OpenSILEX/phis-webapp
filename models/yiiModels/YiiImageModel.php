<?php

//**********************************************************************************************
//                                       YiiImageModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 jan. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSImageModel;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use \app\models\wsModels\WSConstants;
use Yii;

/**
 * The yii model for the images. 
 * Implements a customized Active Record (WSActiveRecord, for the web services 
 * access)
 * @update [Andréas Garcia] 25 Jan., 2019: change "concern" occurences to "concernedItem"
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiImageModel extends WSActiveRecord {
    
    /**
     * the uri of the image (e.g http://www.phenome-fppn.fr/platform/2017/i170000000000)
     * @var string 
     */
    public $uri;
    const URI = "uri";
    /**
     * the uri of the rdf type of the image (e.g http://www.phenome-fppn.fr/vocabulary/2017#HemisphericalImage)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the items concerned by the image
     * @var mixed (array<YiiConcernModel> or string)
     * @see YiiConcernedItemModel
     */
    public $concernedItems;
    const CONCERNED_ITEMS = "concernedItems";
    /**
     * the shooting configuration of the image
     * @var YiiShootingConfiguration
     * @see YiiShootingConfiguration
     */
    public $shootingConfiguration;
    const SHOOTING_CONFIGURATION = "shootingConfiguration";
    /**
     * the informations about the image file
     * @var mixed (YiiFileInformations or string corresponding to the date)
     * @see YiiFileInformations
     */
    public $fileInformations;
    const FILE_INFORMATIONS = "fileInformations";
    const DATE = "date";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSImageModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSImageModel();
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
          [['uri', 'rdfType'], 'string', 'max' => 255],
          [['concernedItems', 'shootingConfiguration', 'fileInformations'], 'safe']
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
            'concernedItems' => Yii::t('app', 'Concerned Items'),
            'shootingConfiguration' => Yii::t('app', 'Shooting Configuration'),
            'fileInformations' => Yii::t('app', 'File Informations')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of an image
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiImageModel::URI];
        $this->rdfType = $array[YiiImageModel::RDF_TYPE];
        
        foreach ($array[YiiImageModel::CONCERNED_ITEMS] as $concernedItemArray) {
            $concernedItem = new YiiConcernedItemModel();
            $concernedItem->arrayToAttributes($concernedItemArray);
            $this->concernedItems[] = $concernedItem;
        }
        
        $shootingConfiguration = new YiiShootingConfigurationModel();
        $shootingConfiguration->arrayToAttributes($array[YiiImageModel::SHOOTING_CONFIGURATION]);
        $this->shootingConfiguration = $shootingConfiguration;
        
        $fileInformations = new YiiFileInformationsModel();
        $fileInformations->arrayToAttributes($array[YiiImageModel::FILE_INFORMATIONS]);
        $this->fileInformations = $fileInformations;
    }

    /**
     * Create an array representing the image metadata
     * Used for the web service for example
     * @warning Actually implemented only for the search fonctionnality
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $attributesArray[YiiImageModel::URI] = $this->uri;
        $attributesArray[YiiImageModel::RDF_TYPE] = $this->rdfType;
        if (is_string($this->concernedItems)) {
            $attributesArray[YiiImageModel::CONCERNED_ITEMS] = $this->concernedItems;
        }
        if (is_string($this->fileInformations)) {
            $attributesArray[YiiImageModel::DATE] = $this->fileInformations;
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
        $imageConceptUri = "http://www.phenome-fppn.fr/vocabulary/2017#Image";
        
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
                if (isset($requestRes[WSConstants::TOKEN])) {
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
