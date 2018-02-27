<?php

//**********************************************************************************************
//                                       YiiLayerModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 29 2017
// Subject: The Yii model for the Layers. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSLayerModel;

/**
 * The yii model for the layers. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSLayerModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiLayerModel extends WSActiveRecord {

    /**
     * the principal object's uri represented by the layer
     *  (e.g. http://phenome-fppn.fr/diaphen/DIA2017-1)
     * @var string
     */
    public $objectURI;
    const OBJECT_URI = "objectUri";
    /**
     * the type of the principal object represented by the layer
     *  (e.g.  http://www.phenome-fppn.fr/vocabulary/2017#Experiment)
     * @var string
     */
    public $objectType;
    const OBJECT_TYPE = "objectType";
    /**
     * true the object and all it's descendants. 
     * false the object and it's directs child.
     * @var string
     */
    public $depth;
    const DEPTH = "depth";
    /**
     * path to the geojson corresponding to the layer
     * @var string
     */
    public $filePath;
    const FILE_PATH = "filePath";
    /**
     * true if the geojson file has to be generated
     * false if the already existing geojson file is used
     * @var string
     */
    public $generateFile;
    const GENERATE_FILE = "generateFile";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSLayerModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSLayerModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['objectURI', 'objectType', 'depth'], 'required'],
          [['filePath', 'objectType', 'objectURI'], 'string']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
          'objectURI' => Yii::t('app', 'Concerned Element URI'),
          'objectType' => Yii::t('app', 'Concerned Element Type'),
          'depth' => Yii::t('app', 'All Descendants'),
          'filePath'=> Yii::t('app', 'File Path'),
          'generateFile' => Yii::t('app', 'Generate Layer')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a layer
     */
    protected function arrayToAttributes($array) {
        $this->objectURI = $array[YiiLayerModel::OBJECT_URI];
        $this->objectType = $array[YiiLayerModel::OBJECT_TYPE];
        $this->depth = $array[YiiLayerModel::DEPTH];
        $this->filePath = $array[YiiLayerModel::FILE_PATH];
    }
    
    /**
     * Create an array representing the layer
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService[YiiLayerModel::OBJECT_URI] = $this->objectURI;
        $elementForWebService[YiiLayerModel::OBJECT_TYPE] = $this->objectType;
        $elementForWebService[YiiLayerModel::DEPTH] = $this->depth;
        $elementForWebService[YiiLayerModel::GENERATE_FILE] = $this->generateFile;
        
        return $elementForWebService;
    }
}