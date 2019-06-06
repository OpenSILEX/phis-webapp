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
     * @example http://www.phenome-fppn.fr/diaphen/s18001
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the type uri (concept uri) of the sensor
     * @example http://www.opensilex.org/vocabulary/oeso#RadiationSensor
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the label of the sensor
     * @example par03_p
     * @var string
     */
    public $label;
    const LABEL = "label";
    /**
     * the brand of the sensor
     * @example Skye Instruments
     * @var string
     */
    public $brand; 
    const BRAND = "brand";
    /**
     * the serial number of the sensor 
     * @example E1JFHS849DNSKF8DH
     * @var string 
     */
    public $serialNumber;
    const SERIAL_NUMBER = "serialNumber";
    /**
     * The model of the sensor.
     * @example m001
     * @var string
     */
    public $model;
    const MODEL = "model";
    /**
     * the in service date of the sensor
     * @example 2011-05-01
     * @var string
     */
    public $inServiceDate;
    const IN_SERVICE_DATE = "inServiceDate";
    /**
     * the date of purchase of the sensor
     * @example 2011-01-01
     * @var string
     */
    public $dateOfPurchase;
    const DATE_OF_PURCHASE = "dateOfPurchase";
    /**
     * the date of last calibration of the sensor
     * @example 2017-03-22
     * @var string
     */
    public $dateOfLastCalibration;
    const DATE_OF_LAST_CALIBRATION = "dateOfLastCalibration";
    /**
     * email of the person in charge of the sensor
     * @example user@email.com
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
     * properties of the sensor (corresponding to the sensor profile)
     * @example
     * [
     *      "relation" => "value",
     *      "relation => "value",
     *      ...
     * ]
     * @var array 
     */
    public $properties;
    const PROPERTIES = "properties";
    const RELATION = "relation";
    const VALUE = "value";
    /**
     * variables observed by the sensor
     * e.g.
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
     * Initialize wsModels. In this class, as there is no dedicated service, there 
     * are two wsModels : WSTripletModel and WSUriModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
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
           [['rdfType', 'uri', 'inServiceDate'], 'required'], 
           [['serialNumber', 'model', 'dateOfPurchase', 'dateOfLastCalibration', 'documents','brand', 'label', 'inServiceDate', 'personInCharge', 'properties'], 'safe']
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
            'personInCharge' => Yii::t('app', 'Person In Charge'),
            'properties' => Yii::t('app', 'Sensor Profile')
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
        $this->serialNumber = $array[YiiSensorModel::SERIAL_NUMBER];
        $this->inServiceDate = $array[YiiSensorModel::IN_SERVICE_DATE];
        $this->model = $array[YiiSensorModel::MODEL];
        $this->dateOfLastCalibration = $array[YiiSensorModel::DATE_OF_LAST_CALIBRATION];
        $this->dateOfPurchase = $array[YiiSensorModel::DATE_OF_PURCHASE];
        $this->personInCharge = $array[YiiSensorModel::PERSON_IN_CHARGE];
        if ($array[YiiSensorModel::VARIABLES]) {
            // get_object_vars transforms stdClass to associative array
            $this->variables = get_object_vars($array[YiiSensorModel::VARIABLES]);
        } 
        
    }
    
    /**
     * allows to fill the property attribute with the information of the given array
     * @param array $array array key => value with the properties of a sensor 
     * (corresponding to a sensor profile)
     */
    protected function propertiesArrayToAttributes($array) {
        if ($array[YiiSensorModel::PROPERTIES] !== null) {
            foreach ($array[YiiSensorModel::PROPERTIES] as $property) {
                $propertyToAdd = null;
                $propertyToAdd[YiiSensorModel::RELATION] = $property->relation; 
                $propertyToAdd[YiiSensorModel::VALUE] = $property->value;
                $propertyToAdd[YiiSensorModel::RDF_TYPE] = $property->rdfType;
                $this->properties[] = $property;
            }
        }
    }

    /**
     * Create an array representing the sensor
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiSensorModel::URI] = $this->uri;
        $elementForWebService[YiiSensorModel::RDF_TYPE] = $this->rdfType;
        $elementForWebService[YiiSensorModel::LABEL] = $this->label;
        $elementForWebService[YiiSensorModel::BRAND] = $this->brand;
        $elementForWebService[YiiSensorModel::IN_SERVICE_DATE] = $this->inServiceDate;
        $elementForWebService[YiiSensorModel::PERSON_IN_CHARGE] = $this->personInCharge;
        
        if ($this->serialNumber !== null && $this->serialNumber !== "") {
            $elementForWebService[YiiSensorModel::SERIAL_NUMBER] = $this->serialNumber;
        }
        if ($this->dateOfLastCalibration !== null && $this->dateOfLastCalibration !== "") {
            $elementForWebService[YiiSensorModel::DATE_OF_LAST_CALIBRATION] = $this->dateOfLastCalibration;
        }
        if ($this->dateOfPurchase !== null && $this->dateOfPurchase !== "") {
           $elementForWebService[YiiSensorModel::DATE_OF_PURCHASE] = $this->dateOfPurchase; 
        }
        if ($this->model !== null && $this->model !== "") {
            $elementForWebService[YiiSensorModel::MODEL] = $this->model;
        }
        
        return $elementForWebService;
    }

    /**
     * calls web service and return the list of sensors types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getSensorsTypes($sessionToken) {
        $sensorConceptUri = "http://www.opensilex.org/vocabulary/oeso#SensingDevice";
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
        
        if (!is_string($requestRes) && !is_object($requestRes)) {
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
    
    /**
     * get a sensor's profile by uri
     * @param string $sessionToken
     * @param string $uri
     * @return mixed
     */
    public function getSensorProfile($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        $requestRes = $this->wsModel->getSensorProfile($sessionToken, $uri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
                return $requestRes;
            } else {
                $this->propertiesArrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * insert a sensor profile in the database (by calling web service)
     * @param string $sessionToken
     * @param array $sensorProfile
     * @return the query result
     */
    public function insertProfile($sessionToken, $sensorProfile) {
        $requestRes = $this->wsModel->postSensorProfile($sessionToken, $sensorProfile);
        
        if (is_string($requestRes) && $requestRes === "token") {
            return $requestRes;
        } else if (isset($requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES})) {
            return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * If the key has a "relation" correspondance in the ontology, 
     * return the relation uri else return null
     * @param string $key
     * @return string e.g. http://www.opensilex.org/vocabulary/oeso#width
     */
    public static function getPropertyFromKey($key) {
        if ($key === "height") {
            return Yii::$app->params["height"];
        } elseif ($key === "width") {
            return Yii::$app->params["width"];
        } elseif ($key === "pixelSize") {
            return Yii::$app->params["pixelSize"];
        } elseif (strstr($key, "wavelength")) {
            return Yii::$app->params["wavelength"];
        } elseif ($key === "scanningAngularRange") {
            return Yii::$app->params["scanningAngularRange"];
        } elseif ($key === "scanAngularResolution") {
            return Yii::$app->params["scanAngularResolution"];
        } elseif ($key === "spotWidth") {
            return Yii::$app->params["spotWidth"];
        } elseif ($key === "spotHeight") {
            return Yii::$app->params["spotHeight"];
        } elseif ($key === "halfFieldOfView") {
            return Yii::$app->params["halfFieldOfView"];
        } elseif ($key === "minWavelength") {
            return Yii::$app->params["minWavelength"];
        } elseif ($key === "maxWavelength") {
            return Yii::$app->params["maxWavelength"];
        } elseif ($key === "spectralSamplingInterval") {
            return Yii::$app->params["spectralSamplingInterval"];
        } elseif ($key === "lensUri") {
            return Yii::$app->params["hasLens"];
        } elseif (strstr($key, "focalLength")) {
            return Yii::$app->params["focalLength"];
        } elseif (strstr($key, "attenuatorFilter")) {
            return Yii::$app->params["attenuatorFilter"];
        } elseif (strstr($key, "waveband")) {
            return Yii::$app->params["waveband"];
        }
        
        return null;
    }
    
    /*
     * Update variables measured by a sensor
     * @param string $sessionToken
     * @param string $sensorUri
     * @param array $variablesUri
     * @return the query result
     */
    public function updateVariables($sessionToken, $sensorUri, $variablesUri) {
        $requestRes = $this->wsModel->putSensorVariables($sessionToken, $sensorUri, $variablesUri);
        
        if (is_string($requestRes) && $requestRes === "token") {
            return $requestRes;
        } else if (isset($requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS})) {
            return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS};
        } else {
            return $requestRes;
        }
    }

    /**
     * Get all the sensors uri and label
     * @return array the list of the sensors uri and label existing in the database
     * @example returned array : 
     * [
     *      ["http://www.opensilex.fr/platform/s001"] => "sensor label",
     *      ...
     * ]
     */
    public function getAllSensorsUrisAndLabels($sessionToken) {
        $foundedSensors = $this->find($sessionToken, $this->attributesToArray());
        $sensorsToReturn = [];
        
        if ($foundedSensors !== null) {
            foreach($foundedSensors as $sensor) {
                $sensorsToReturn[$sensor->uri] = $sensor->label;
            }
            
            // If there are other pages, get the other sensors
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextSensors = $this->getAllSensorsUrisAndLabels($sessionToken);

                $sensorsToReturn = array_merge($sensorsToReturn, $nextSensors);
            }
        }
        
        return $sensorsToReturn;
    }
}
