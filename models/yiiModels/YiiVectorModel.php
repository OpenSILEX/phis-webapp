<?php

//******************************************************************************
//                                       YiiVectorModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 5 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  5 avr. 2018
// Subject: The Yii model for the vectors. Used with web services
//******************************************************************************
namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSTripletModel;
use app\models\wsModels\WSUriModel;

use Yii;

/**
 * The yii model for the vectors. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiVectorModel extends WSActiveRecord {
    /**
     * the vector's uri
     *  (e.g. http://www.phenome-fppn.fr/diaphen/v18001)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the type uri (concept uri) of the vector
     *  (e.g. http://www.phenome-fppn.fr/vocabulary/2017#CarSupport)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the label of the vector
     *  (e.g. par03_p)
     * @var string
     */
    public $label;
    const LABEL = "label";
    /**
     * the brand of the vector
     *  (e.g. Skye Instruments)
     * @var string
     */
    public $brand; 
    const BRAND = "brand";
    /**
     * the in service date of the vector
     *  (e.g 2011-05-01)
     * @var string
     */
    public $inServiceDate;
    const IN_SERVICE_DATE = "inServiceDate";
    /**
     * the date of purchase of the sensor
     *  (e.g. 2011-01-01)
     * @var string
     */
    public $dateOfPurchase;
    const DATE_OF_PURCHASE = "dateOfPurchase";
    /**
     * the uri of documents linked to the sensor
     * @var string
     */
    public $documents;
    /**
     * corresponds to the WSTripletModel, used for the insertions of the sensor
     * @var WSTripletModel
     */
    private $wsTripletModel;
    
    /**
     * Initialize wsModels. In this class, as there is no dedicated service, there 
     * are two wsModels : WSTripletModel and WSVectorModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsTripletModel = new WSTripletModel();
//        $this->wsModel = new WSVectorModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
       return [
          [['rdfType', 'brand', 'label'], 'required'],  
          [['inServiceDate', 'dateOfPurchase', 'documents'], 'safe']
        ]; 
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-structure-models.html#attribute-labels
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'uri' => 'URI',
            'rdfType' => Yii::t('app', 'Type'),
            'label' => Yii::t('app', 'Alias'),
            'brand' => Yii::t('app', 'Brand'),
            'inServiceDate' => Yii::t('app', 'In Service Date'),
            'dateOfPurchase' => Yii::t('app', 'Date Of Purchase')
        ];
    }
    
    protected function arrayToAttributes($array) {
        //todo
    }

    public function attributesToArray() {
        //todo
    }
    
    /**
     * calls web service and return the list of vectors types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getVectorsTypes($sessionToken) {
        $vectorConceptUri = "http://www.phenome-fppn.fr/vocabulary/2017#Vector";
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $vectorConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param array $sensors
     * @return string|array 
     */
    public function createVectors($sessionToken, $sensors) {
        $requestRes = $this->wsTripletModel->post($sessionToken, "", $sensors);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes->{\app\models\wsModels\WSConstants::TOKEN})) {
                return $requestRes;
            } else {
                return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES};
            }
        } else {
            return $requestRes;
        }
    }
}