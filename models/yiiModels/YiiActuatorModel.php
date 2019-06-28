<?php

//******************************************************************************
//                                       YiiActuatorModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSActuatorModel;

use Yii;

/**
 * The yii model for the actuators. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSUriModel
 * @see app\models\wsModels\WSActiveRecord
 * @see app\models\wsModels\WSActuatorModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiActuatorModel extends WSActiveRecord {
    
    /**
     * the actuator's uri
     * @example http://www.opensilex.org/demo/2018/a18001
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the type uri (concept uri) of the actuator
     * @example http://www.opensilex.org/vocabulary/oeso#Actuator
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the label of the actuators
     * @example par03_p
     * @var string
     */
    public $label;
    const LABEL = "label";
    /**
     * the brand of the actuators
     * @example Skye Instruments
     * @var string
     */
    public $brand; 
    const BRAND = "brand";
    /**
     * the serial number of the actuators 
     * @example E1JFHS849DNSKF8DH
     * @var string 
     */
    public $serialNumber;
    const SERIAL_NUMBER = "serialNumber";
    /**
     * the model of the actuators 
     * @example mod01
     * @var string 
     */
    public $model;
    const MODEL = "model";
    /**
     * the in service date of the actuators
     * @example 2011-05-01
     * @var string
     */
    public $inServiceDate;
    const IN_SERVICE_DATE = "inServiceDate";
    /**
     * the date of purchase of the actuators
     * @example 2011-01-01
     * @var string
     */
    public $dateOfPurchase;
    const DATE_OF_PURCHASE = "dateOfPurchase";
    /**
     * the date of last calibration of the actuators
     * @example 2017-03-22
     * @var string
     */
    public $dateOfLastCalibration;
    const DATE_OF_LAST_CALIBRATION = "dateOfLastCalibration";
    /**
     * email of the person in charge of the actuators
     * @example user@email.com
     * @var string
     */
    public $personInCharge;
    const PERSON_IN_CHARGE = "personInCharge";
    /**
     * the uri of documents linked to the actuators
     * @var string
     */
    public $documents;
    /**
     * variables observed by the actuators
     * @example
     * [
     *      "uri" => "label",
     *      "uri" => "label",
     *      ...
     * ]
     * @var array 
     */
    public $variables;
    const VARIABLES = "variables";
    /**
     * Initialize wsModel.
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSActuatorModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
       return [ 
           [['rdfType', 'uri', 'brand', 'personInCharge', 'label'], 'required'], 
           [['serialNumber', 'model', 'dateOfPurchase', 'dateOfLastCalibration', 'documents',
              'brand', 'label', 'inServiceDate', 'personInCharge', 'properties'], 'safe']
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
            'serialNumber'=> Yii::t('app', 'Serial Number'),
            'inServiceDate' => Yii::t('app', 'In Service Date'),
            'model' => Yii::t('app', 'Model'),
            'dateOfPurchase' => Yii::t('app', 'Date Of Purchase'),
            'dateOfLastCalibration' => Yii::t('app', 'Date Of Last Calibration'),
            'personInCharge' => Yii::t('app', 'Person In Charge')
        ];
    }
    
    /**
     * Allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a sensor
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiSensorModel::URI];
        $this->rdfType = $array[YiiSensorModel::RDF_TYPE];
        $this->label = $array[YiiSensorModel::LABEL];
        $this->brand = $array[YiiSensorModel::BRAND];
        $this->serialNumber = $array[YiiSensorModel::SERIAL_NUMBER];
        $this->model = $array[YiiSensorModel::MODEL];
        $this->inServiceDate = $array[YiiSensorModel::IN_SERVICE_DATE];
        $this->dateOfLastCalibration = $array[YiiSensorModel::DATE_OF_LAST_CALIBRATION];
        $this->dateOfPurchase = $array[YiiSensorModel::DATE_OF_PURCHASE];
        $this->personInCharge = $array[YiiSensorModel::PERSON_IN_CHARGE];
        if ($array[YiiSensorModel::VARIABLES]) {
            // get_object_vars transforms stdClass to associative array
            $this->variables = get_object_vars($array[YiiSensorModel::VARIABLES]);
        } 
        
    }

    /**
     * Create an array representing the actuator
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiSensorModel::URI] = $this->uri;
        $elementForWebService[YiiSensorModel::RDF_TYPE] = $this->rdfType;
        $elementForWebService[YiiSensorModel::LABEL] = $this->label;
        $elementForWebService[YiiSensorModel::BRAND] = $this->brand;
        
        $elementForWebService[YiiSensorModel::PERSON_IN_CHARGE] = $this->personInCharge;
        if (!empty($this->inServiceDate)) {
            $elementForWebService[YiiSensorModel::IN_SERVICE_DATE] = $this->inServiceDate;
        }
        
        if (!empty($this->serialNumber)) {
            $elementForWebService[YiiSensorModel::SERIAL_NUMBER] = $this->serialNumber;
        }
        if (!empty($this->model)) {
            $elementForWebService[YiiSensorModel::MODEL] = $this->model;
        }
        if (!empty($this->dateOfLastCalibration)) {
            $elementForWebService[YiiSensorModel::DATE_OF_LAST_CALIBRATION] = $this->dateOfLastCalibration;
        }
        if (!empty($this->dateOfPurchase)) {
           $elementForWebService[YiiSensorModel::DATE_OF_PURCHASE] = $this->dateOfPurchase; 
        }
        
        return $elementForWebService;
    }

    /**
     * calls web service and return the list of actuators types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the actuators types
     */
    public function getActuatorsTypes($sessionToken) {
        $actuatorConceptUri = Yii::$app->params["Actuator"];
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $actuatorConceptUri, $params);
        
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
     * Get the informations of an actuator.
     * @param string $sessionToken user session token
     * @param string $uri uri of the actuator
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        $requestRes = $this->wsModel->getActuatorByUri($sessionToken, $uri, $params);
        
        if (!is_string($requestRes) && !is_object($requestRes)) {
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
    
    /**
     * Update variables measured by an actuator
     * @param string $sessionToken
     * @param string $actuatorUri
     * @param array $variablesUri
     * @return the query result
     */
    public function updateVariables($sessionToken, $actuatorUri, $variablesUri) {
        $requestRes = $this->wsModel->putSensorVariables($sessionToken, $actuatorUri, $variablesUri);
        
        if (is_string($requestRes) && $requestRes === "token") {
            return $requestRes;
        } else if (isset($requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS})) {
            return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS};
        } else {
            return $requestRes;
        }
    }

    /**
     * Get all the actuator uri and label
     * @return array the list of the actuator uri and label existing in the database
     * @example returned array : 
     * [
     *      ["http://www.opensilex.fr/platform/a001"] => "actuator label",
     *      ...
     * ]
     */
    public function getAllActuatorsUrisAndLabels($sessionToken) {
        $foundedActuators = $this->find($sessionToken, $this->attributesToArray());
        $actuatorsToReturn = [];
        
        if ($foundedActuators !== null) {
            foreach($foundedActuators as $actuator) {
                $actuatorsToReturn[$actuator->uri] = $actuator->label;
            }
            
            // If there are other pages, get the other actuators
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextActuators = $this->getAllActuatorsUrisAndLabels($sessionToken);

                $actuatorsToReturn = array_merge($actuatorsToReturn, $nextActuators);
            }
        }
        
        return $actuatorsToReturn;
    }
}
