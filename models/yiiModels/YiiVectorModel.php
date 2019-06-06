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
use app\models\wsModels\WSVectorModel;

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
     *  (e.g. http://www.opensilex.org/vocabulary/oeso#CarSupport)
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
     * the serial number of the vector
     * @var string
     */
    public $serialNumber;
    const SERIAL_NUMBER = "serialNumber";
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
     * email of the person in charge of the sensor (must be a person declared in PHIS)
     * (e.g morgane.vidal@inra.fr)
     * @var string
     */
    public $personInCharge;
    const PERSON_IN_CHARGE = "personInCharge";
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
        $this->wsModel = new WSVectorModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
       return [
          [['rdfType', 'brand', 'label', 'personInCharge', 'inServiceDate'], 'required'],  
          [['serialNumber', 'dateOfPurchase', 'documents'], 'safe']
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
            'serialNumber' => Yii::t('app', 'Serial Number'),
            'inServiceDate' => Yii::t('app', 'In Service Date'),
            'dateOfPurchase' => Yii::t('app', 'Date Of Purchase'),
            'personInCharge' => Yii::t('app', 'Person In Charge')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a sensor
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiVectorModel::URI];
        $this->rdfType = $array[YiiVectorModel::RDF_TYPE];
        $this->label = $array[YiiVectorModel::LABEL];
        $this->brand = $array[YiiVectorModel::BRAND];
        $this->serialNumber = $array[YiiVectorModel::SERIAL_NUMBER];
        $this->inServiceDate = $array[YiiVectorModel::IN_SERVICE_DATE];
        $this->dateOfPurchase = $array[YiiVectorModel::DATE_OF_PURCHASE];
        $this->personInCharge = $array[YiiVectorModel::PERSON_IN_CHARGE];
    }

    /**
     * Create an array representing the vector
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiVectorModel::URI] = $this->uri;
        $elementForWebService[YiiVectorModel::RDF_TYPE] = $this->rdfType;
        $elementForWebService[YiiVectorModel::LABEL] = $this->label;
        $elementForWebService[YiiVectorModel::BRAND] = $this->brand;
        $elementForWebService[YiiVectorModel::IN_SERVICE_DATE] = $this->inServiceDate;
        $elementForWebService[YiiVectorModel::PERSON_IN_CHARGE] = $this->personInCharge;
        
        if ($this->serialNumber != null) {
            $elementForWebService[YiiVectorModel::SERIAL_NUMBER] = $this->serialNumber;
        }
        
        if ($this->dateOfPurchase != null) {
            $elementForWebService[YiiVectorModel::DATE_OF_PURCHASE] = $this->dateOfPurchase;
        }
        
        return $elementForWebService;
    }
    
    /**
     * calls web service and return the list of vectors types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getVectorsTypes($sessionToken) {
        $vectorConceptUri = "http://www.opensilex.org/vocabulary/oeso#Vector";
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
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * get vector's informations by uri
     * @param string $sessionToken user session token
     * @param string $uri sensor's uri
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        $requestRes = $this->wsModel->getVectorByUri($sessionToken, $uri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
                return $requestRes;
            } else {
                $this->arrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }
}