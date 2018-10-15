<?php

//******************************************************************************
//                          YiiRadiometricTargetModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 27 September, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSRadiometricTargetModel;
use Yii;

/**
 * The yii model for the radiometric targets. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * 
 * @see app\models\wsModels\WSTripletModel
 * @see app\models\wsModels\WSUriModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Migot Vincent <vincent.migot@inra.fr>
 */
class YiiRadiometricTargetModel extends WSActiveRecord {

    /**
     * the radiometric target's uri
     *  (e.g. http://www.phenome-fppn.fr/diaphen/s18001)
     * @var string
     */
    public $uri;
    const URI = "uri";

    /**
     * the label of the radiometric target
     *  (e.g. rt00001)
     * @var string
     */
    public $label;
    const LABEL = "label";

    /**
     * the brand of the radiometric target
     *  (e.g. Skye Instruments)
     * @var string
     */
    public $brand;
    const BRAND = "brand";

    /**
     * the serial number of the radiometric target 
     *  (e.g. E1JFHS849DNSKF8DH)
     * @var string 
     */
    public $serialNumber;
    const SERIAL_NUMBER = "serialNumber";

    /**
     * the in service date of the radiometric target
     *  (e.g 2011-05-01)
     * @var string
     */
    public $inServiceDate;
    const IN_SERVICE_DATE = "inServiceDate";

    /**
     * the date of purchase of the radiometric target
     *  (e.g. 2011-01-01)
     * @var string
     */
    public $dateOfPurchase;
    const DATE_OF_PURCHASE = "dateOfPurchase";

    /**
     * the date of last calibration of the radiometric target
     *  (e.g 2017-03-22)
     * @var string
     */
    public $dateOfLastCalibration;
    const DATE_OF_LAST_CALIBRATION = "dateOfLastCalibration";

    /**
     * email of the person in charge of the radiometric target
     *  (e.g. user@email.com)
     * @var string
     */
    public $personInCharge;
    const PERSON_IN_CHARGE = "personInCharge";

    /**
     * material of the radiometric target
     *  (e.g. spectralon)
     * @var string
     */
    public $material;
    const MATERIAL = "material";

    /**
     * shape of the radiometric target
     *  (e.g. circular or rectangular)
     * @var string
     */
    public $shape;
    const SHAPE = "shape";

    /**
     * length of the radiometric target if shape is rectangular
     *  (e.g. 3.14)
     * @var number
     */
    public $length;
    const LENGTH = "length";

    /**
     * width of the radiometric target if shape is rectangular
     *  (e.g. 3.14)
     * @var number
     */
    public $width;
    const WIDTH = "width";

    /**
     * diameter of the radiometric target if shape is circualr
     *  (e.g. 3.14)
     * @var number
     */
    public $diameter;
    const DIAMETER = "diameter";

    /**
     * BRDF parameter of the radiometric target
     *  (e.g. 5.1)
     * @var number
     */
    public $brdfP1;
    const BRDFP1 = "brdfP1";

    /**
     * BRDF parameter of the radiometric target
     *  (e.g. 5.2)
     * @var number
     */
    public $brdfP2;
    const BRDFP2 = "brdfP2";

    /**
     * BRDF parameter of the radiometric target
     *  (e.g. 5.3)
     * @var number
     */
    public $brdfP3;
    const BRDFP3 = "brdfP3";

    /**
     * BRDF parameter of the radiometric target
     *  (e.g. 5.4)
     * @var number
     */
    public $brdfP4;
    const BRDFP4 = "brdfP4";

    /**
     * Reflectance file name of the radiometric target
     *  (e.g. 5.1)
     * @var number
     */

    public $reflectanceFile;
    const REFLECTANCE_FILE = "reflectanceFile";

    /**
     * Initialize wsModels for radioemtric target
     * 
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSRadiometricTargetModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }

    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['uri', 'label', 'brand', 'inServiceDate', 'personInCharge', 'material', 'shape', 'reflectanceFile'], 'required'],
            [['serialNumber', 'dateOfPurchase', 'dateOfLastCalibration', 'length', 'width', 'diameter', 'brdfP1', 'brdfP2', 'brdfP3', 'brdfP4'], 'safe'],
            [['reflectanceFile'], 'file']
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
            'dateOfLastCalibration' => Yii::t('app', 'Date Of Last Calibration'),
            'personInCharge' => Yii::t('app', 'Person In Charge'),
            'material' => Yii::t('app', 'Material'),
            'shape' => Yii::t('app', 'Shape'),
            'width' => Yii::t('app', 'Width'),
            'length' => Yii::t('app', 'Length'),
            'diameter' => Yii::t('app', 'Diameter'),
            'brdfP1' => Yii::t('app', 'BRDF coefficient P1'),
            'brdfP2' => Yii::t('app', 'BRDF coefficient P2'),
            'brdfP3' => Yii::t('app', 'BRDF coefficient P3'),
            'brdfP4' => Yii::t('app', 'BRDF coefficient P4'),
            'reflectanceFile' => Yii::t('app', 'Spectral hemispheric reflectance file')
        ];
    }

    /**
     * Get the properties of a radiometric target corresponding to the given uri
     * 
     * @param type $sessionToken
     * @param type $uri
     * @return $this
     */
    public function getDetails($sessionToken, $uri) {
        $requestRes = $this->wsModel->getDetails($sessionToken, $uri);

        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->uri = $uri;
                $this->arrayToAttributes($requestRes);
                return $this;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * 
     * @param array $array array key => value which contains the metadata of 
     *                     a radiometric target
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiRadiometricTargetModel::URI];
        
        foreach ($array[YiiModelsConstants::PROPERTIES] as $property) {
            switch($property->relation) {
                case Yii::$app->params['hasBrand']:
                    $this->brand = $property->value;
                    break;
                case Yii::$app->params['serialNumber']:
                    $this->serialNumber = $property->value;
                    break;                
                case Yii::$app->params['inServiceDate']:
                    $this->inServiceDate = $property->value;
                    break;
                case Yii::$app->params['dateOfPurchase']:
                    $this->dateOfPurchase = $property->value;
                    break;                
                case Yii::$app->params['dateOfLastCalibration']:
                    $this->dateOfLastCalibration = $property->value;
                    break;                
                case Yii::$app->params['personInCharge']:
                    $this->personInCharge = $property->value;
                    break;
                case Yii::$app->params['hasRadiometricTargetMaterial']:
                    $this->material = $property->value;
                    break;
                case Yii::$app->params['hasShape']:
                    $this->shape = $property->value;
                    break;
                case Yii::$app->params['hasShapeLength']:
                    $this->length = $property->value;
                    break;
                case Yii::$app->params['hasShapeWidth']:
                    $this->width = $property->value;
                    break;
                case Yii::$app->params['hasShapeDiameter']:
                    $this->diameter = $property->value;
                    break;
                case Yii::$app->params['brdfP1']:
                    $this->brdfP1 = $property->value;
                    break;
                case Yii::$app->params['brdfP2']:
                    $this->brdfP2 = $property->value;
                    break;
                case Yii::$app->params['brdfP3']:
                    $this->brdfP3 = $property->value;
                    break;
                case Yii::$app->params['brdfP4']:
                    $this->brdfP4 = $property->value;
                    break;
                case Yii::$app->params['rdfsLabel']:
                    $this->label = $property->value;
                     break;
                default:
                    break;
            }
        }
    }

    /**
     * Create an array representing the radiometric target
     * Used for the web service for example
     * 
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService[YiiModelsConstants::PAGE] = $this->page <= 0 ? 0 : $this->page - 1;
        $elementForWebService[YiiModelsConstants::PAGE_SIZE] = $this->pageSize;

        $elementForWebService[YiiRadiometricTargetModel::URI] = $this->uri;
        $elementForWebService[YiiRadiometricTargetModel::LABEL] = $this->label;
        $elementForWebService[YiiRadiometricTargetModel::BRAND] = $this->brand;
        $elementForWebService[YiiRadiometricTargetModel::IN_SERVICE_DATE] = $this->inServiceDate;
        $elementForWebService[YiiRadiometricTargetModel::PERSON_IN_CHARGE] = $this->personInCharge;
        $elementForWebService[YiiRadiometricTargetModel::MATERIAL] = $this->material;
        $elementForWebService[YiiRadiometricTargetModel::SHAPE] = $this->shape;
        $elementForWebService[YiiRadiometricTargetModel::REFLECTANCE_FILE] = $this->reflectanceFile;

        if ($this->serialNumber !== null) {
            $elementForWebService[YiiRadiometricTargetModel::SERIAL_NUMBER] = $this->serialNumber;
        }
        if ($this->dateOfLastCalibration !== null) {
            $elementForWebService[YiiRadiometricTargetModel::DATE_OF_LAST_CALIBRATION] = $this->dateOfLastCalibration;
        }
        if ($this->dateOfPurchase !== null) {
            $elementForWebService[YiiRadiometricTargetModel::DATE_OF_PURCHASE] = $this->dateOfPurchase;
        }

        if ($this->length !== null) {
            $elementForWebService[YiiRadiometricTargetModel::LENGTH] = $this->length;
        }
        if ($this->width !== null) {
            $elementForWebService[YiiRadiometricTargetModel::WIDTH] = $this->width;
        }
        if ($this->diameter !== null) {
            $elementForWebService[YiiRadiometricTargetModel::DIAMETER] = $this->diameter;
        }

        if ($this->brdfP1 !== null) {
            $elementForWebService[YiiRadiometricTargetModel::BRDFP1] = $this->brdfP1;
        }
        if ($this->brdfP2 !== null) {
            $elementForWebService[YiiRadiometricTargetModel::BRDFP2] = $this->brdfP2;
        }
        if ($this->brdfP3 !== null) {
            $elementForWebService[YiiRadiometricTargetModel::BRDFP3] = $this->brdfP3;
        }
        if ($this->brdfP4 !== null) {
            $elementForWebService[YiiRadiometricTargetModel::BRDFP4] = $this->brdfP4;
        }

        return $elementForWebService;
    }

    /**
     * Return the radiometric target model as an array for the webservice
     * 
     * @return Array list of rdf properties with uri and label
     * eg.
     * [
     *      uri => 'http://www.phenome-fppn.fr/id/radiometricTargets/rt001',
     *      label => 'radiometric target label'
     *      properties => [
     *          [
     *              relation => 'http://www.phenome-fppn.fr/vocabulary/2017#hasBrand'
     *              value => 'brand name'
     *          ],
     *          [
     *              relation => 'http://www.phenome-fppn.fr/vocabulary/2017#hasRadiometricTargetMaterial'
     *              value => 'spectralon'
     *          ],...
     *      ]
     * ]
     */
    public function mapToProperties() {
        $elementForWebService = [];

        if (!$this->isNewRecord) {
            $elementForWebService[YiiRadiometricTargetModel::URI] = $this->uri;
        }

        $elementForWebService[YiiRadiometricTargetModel::LABEL] = $this->label;

        $properties = [];

        $properties[] = [
            "relation" =>  Yii::$app->params['hasBrand'],
            "value" => $this->brand
        ];

        $properties[] = [
            "relation" => Yii::$app->params['inServiceDate'],
            "value" => $this->inServiceDate
        ];
        
        $properties[] = [
            "relation" => Yii::$app->params['hasTechnicalContact'],
            "value" => $this->personInCharge
        ];
        
        $properties[] = [
            "relation" => Yii::$app->params['hasRadiometricTargetMaterial'],
            "value" => $this->material
        ];
        $properties[] = [
            "relation" => Yii::$app->params['hasShape'],
            "value" => $this->shape
        ];

        if ($this->serialNumber) {
            $properties[] = [
                "relation" =>  Yii::$app->params['serialNumber'],
                "value" => $this->serialNumber
            ];
        }
        if ($this->dateOfLastCalibration) {
            $properties[] = [
                "relation" => Yii::$app->params['dateOfLastCalibration'],
                "value" => $this->dateOfLastCalibration
            ];
        }
        if ($this->dateOfPurchase) {
            $properties[] = [
                "relation" => Yii::$app->params['dateOfPurchase'],
                "value" => $this->dateOfPurchase
            ];
        }

        if ($this->length) {
            $properties[] = [
                "relation" => Yii::$app->params['hasShapeLength'],
                "value" => $this->length
            ];
        }
        if ($this->width) {
            $properties[] = [
                "relation" => Yii::$app->params['hasShapeWidth'],
                "value" => $this->width
            ];
        }
        if ($this->diameter) {
            $properties[] = [
                "relation" => Yii::$app->params['hasShapeDiameter'],
                "value" => $this->diameter
            ];
        }

        if ($this->brdfP1) {
            $properties[] = [
                "relation" => Yii::$app->params['brdfP1'],
                "value" => $this->brdfP1
            ];
        }
        if ($this->brdfP2) {
            $properties[] = [
                "relation" => Yii::$app->params['brdfP2'],
                "value" => $this->brdfP2
            ];
        }
        if ($this->brdfP3) {
            $properties[] = [
                "relation" => Yii::$app->params['brdfP3'],
                "value" => $this->brdfP3
            ];
        }
        if ($this->brdfP4) {
            $properties[] = [
                "relation" => Yii::$app->params['brdfP4'],
                "value" => $this->brdfP4
            ];
        }

        $elementForWebService[YiiModelsConstants::PROPERTIES] = $properties;
        
        return $elementForWebService;
    }

}
