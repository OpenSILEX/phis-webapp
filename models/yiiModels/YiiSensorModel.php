<?php

//******************************************************************************
//                                       YiiSensorModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: The Yii model for the sensors. Used with web services
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSTripletModel;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSSensorModel;

use Yii;

/**
 * The yii model for the sensors. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSUriModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiSensorModel extends WSActiveRecord {
    
    /**
     * the sensor's uri
     *  (e.g. http://www.phenome-fppn.fr/diaphen/s18001)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the type uri (concept uri) of the sensor
     *  (e.g. http://www.phenome-fppn.fr/vocabulary/2017#RadiationSensor)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the label of the sensor
     *  (e.g. par03_p)
     * @var string
     */
    public $label;
    const LABEL = "label";
    /**
     * the brand of the sensor
     *  (e.g. Skye Instruments)
     * @var string
     */
    public $brand; 
    const BRAND = "brand";
    /**
     * the in service date of the sensor
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
     * the date of last calibration of the sensor
     *  (e.g 2017-03-22)
     * @var string
     */
    public $dateOfLastCalibration;
    const DATE_OF_LAST_CALIBRATION = "dateOfLastCalibration";
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
     * corresponds to the WSUriModel, used for the gets of the sensor
     * @var WSUriModel
     */
    private $wsUriModel;
    
    /**
     * Initialize wsModels. In this class, as there is no dedicated service, there 
     * are two wsModels : WSTripletModel and WSUriModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsTripletModel = new WSTripletModel();
        $this->wsUriModel = new WSUriModel();
        $this->wsModel = new WSSensorModel();
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
          [['inServiceDate', 'dateOfPurchase', 'dateOfLastCalibration', 'documents'], 'safe']
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
            'dateOfPurchase' => Yii::t('app', 'Date Of Purchase'),
            'dateOfLastCalibration' => Yii::t('app', 'Date Of Last Calibration')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a sensor
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiSensorModel::URI];
        $this->rdfType = $array[YiiSensorModel::RDF_TYPE];
        $this->label = $array[YiiSensorModel::LABEL];
        $this->brand = $array[YiiSensorModel::BRAND];
        $this->inServiceDate = $array[YiiSensorModel::IN_SERVICE_DATE];
        $this->dateOfLastCalibration = $array[YiiSensorModel::DATE_OF_LAST_CALIBRATION];
        $this->dateOfPurchase = $array[YiiSensorModel::DATE_OF_PURCHASE];
    }

    /**
     * Create an array representing the sensor
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService[YiiSensorModel::URI] = $this->uri;
        $elementForWebService[YiiSensorModel::RDF_TYPE] = $this->rdfType;
        $elementForWebService[YiiSensorModel::LABEL] = $this->label;
        $elementForWebService[YiiSensorModel::BRAND] = $this->brand;
        $elementForWebService[YiiSensorModel::IN_SERVICE_DATE] = $this->inServiceDate;
        $elementForWebService[YiiSensorModel::DATE_OF_LAST_CALIBRATION] = $this->dateOfLastCalibration;
        $elementForWebService[YiiSensorModel::DATE_OF_PURCHASE] = $this->dateOfPurchase;
        
        return $elementForWebService;
    }
    
    /**
     * calls web service and return the list of sensors types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getSensorsTypes($sessionToken) {
        $sensorConceptUri = "http://www.phenome-fppn.fr/vocabulary/2017#SensingDevice";
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $sensorConceptUri, $params);
        
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
    public function createSensors($sessionToken, $sensors) {
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
    
    /**
     * get sensor's informations by uri
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
        $requestRes = $this->wsModel->getSensorByUri($sessionToken, $uri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
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
