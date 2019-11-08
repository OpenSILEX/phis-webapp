<?php

//**********************************************************************************************
//                                       YiiDatasetModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 2 2017
// Subject: The Yii model for the dataset. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSDatasetModel;

/**
 * Model for the dataset. Used with web services
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiDatasetModel extends WSActiveRecord {

    /**
     * Provenance uri
     * @var string 
     */
    public $provenanceUri;
    
    /**
     * provenance alias
     * @var string 
     */
    public $provenanceAlias;

    const ALIAS = "provenanceAlias";

    /**
     * provenance alias
     * @var string 
     */
    public $provenanceComment;

    const COMMENT = "provenanceComment";

    /**
     * dataset data variable
     * @var string
     */
    public $variables;

    const VARIABLE_URI = "variableUri";

    /**
     * uri of the linked documents
     * @var array<string>
     */
    public $documentsURIs;

    const DOCUMENTS_URIS = "documentsUris";

    /**
     * contains data. data["uriAO"], data["date"], data["value"] 
     * @var array 
     */
    public $data;

    /**
     * data generating script
     * @var file
     */
    public $file;
    
     /**
     * Sensor uris
     * @var array 
     */
    public $provenanceSensingDevices;
    
      /**
     * Agent uris
     * @var array 
     */
    public $provenanceAgents;

    const PROVENANCE = "provenance";
    const DATA = "data";

    /**
     * 
     * @param string $pageSize number of elements per page
     * @param string $page current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSDatasetModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }

    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['variables', 'provenanceAlias', 'file', 'provenanceUri'], 'required'],
            [['provenanceSensingDevices'], 'safe'],
            [['provenanceAgents'], 'safe'],
            [['provenanceComment'], 'string'],
            [['provenanceUri', 'provenanceComment', 'documentsURIs', 'data', 'file'], 'safe'],
            [['file'], 'file', 'extensions' => 'csv']
        ];
    }

    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'provenanceUri' => Yii::t('app', 'Provenance (URI)'),
            'provenanceComment' => Yii::t('app', 'Provenance comment'),
            'provenanceSensingDevices' => Yii::t('app', 'Sensor'),
            'provenanceAgents' => Yii::t('app', 'Agent'),
            'variables' => Yii::t('app', 'Variable(s)'),
            'file' => Yii::t('app', 'Data file'),
            'documentsUris' => Yii::t('app', 'Documents')
        ];
    }

    /**
     * allows to fill the attributes with the informations in the array given 
     * @warning unimplement yet
     * @param array $array array key => value which contains the metadata of a dataset
     */
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
    }

    /**
     * Create an array representing the dataset
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        throw new Exception('Not implemented');
    }

}
