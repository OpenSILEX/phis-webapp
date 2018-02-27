<?php

//**********************************************************************************************
//                                       YiiImageModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: The Yii model for the images. 
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSImageModel;
use app\models\wsModels\WSActiveRecord;
use Yii;

/**
 * The yii model for the images. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
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
     * the elements concerned by the image
     * @var mixed (array<YiiConcernModel> or string)
     * @see YiiConcernModel
     */
    public $concern;
    const CONCERN = "concern";
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
          [['concern', 'shootingConfiguration', 'fileInformations'], 'safe']
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
            'concern' => Yii::t('app', 'Concerned Elements'),
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
        
        foreach ($array[YiiImageModel::CONCERN] as $concernArray) {
            $concern = new YiiConcernModel();
            $concern->arrayToAttributes($concernArray);
            $this->concern[] = $concern;
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
        $toReturn[YiiImageModel::URI] = $this->uri;
        $toReturn[YiiImageModel::RDF_TYPE] = $this->rdfType;
        if (is_string($this->concern)) {
            $toReturn[YiiImageModel::CONCERNED_ITEMS] = $this->concern;
        }
        if (is_string($this->fileInformations)) {
            $toReturn[YiiImageModel::DATE] = $this->fileInformations;
        }
        if ($this->page != null) {
            $toReturn[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        if ($this->pageSize != null) {
            $toReturn[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        
        return $toReturn;
    }
    
    /**
     * get the list of the images types defined in the ontology.
     * @return the list of rdf types of images
     */
    public function getRdfTypes() {
        //SILEX:todo
        //when the service to get the types will be developped, 
        //implements here
        //\SILEX:todo
        return [
          "http://www.phenome-fppn.fr/vocabulary/2017#HemisphericalImage"  => Yii::t('app', 'Hemisphericals'),
//          "http://www.phenome-fppn.fr/vocabulary/2017#RGBImage"  => Yii::t('app', 'RGBImage'),

        ];
    }
}
