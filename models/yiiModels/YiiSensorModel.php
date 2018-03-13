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
    /**
     * the type uri (concept uri) of the sensor
     *  (e.g. http://www.phenome-fppn.fr/vocabulary/2017#RadiationSensor)
     * @var string
     */
    public $rdfType;
    /**
     * the alias of the sensor
     *  (e.g. par03_p)
     * @var string
     */
    public $alias;
    /**
     * the brand of the sensor
     *  (e.g. Skye Instruments)
     * @var string
     */
    public $brand; 
    /**
     * the uri of the variable measured by the sensor
     *  (e.g. http://www.phenome-fppn.fr/phenovia/id/variables/v001)
     * @var string
     */
    public $variable;
    /**
     * the in service date of the sensor
     *  (e.g 2011-05-01)
     * @var string
     */
    public $inServiceDate;
    /**
     * the date of purchase of the sensor
     *  (e.g. 2011-01-01)
     * @var string
     */
    public $dateOfPurchase;
    /**
     * the date of last calibration of the sensor
     *  (e.g 2017-03-22)
     * @var string
     */
    public $dateOfLastCalibration;
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
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     * @return array the rules of the attributes
     */
    public function rules() {
       return [
          [['rdfType', 'brand', 'variable', 'alias'], 'required'],  
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
            'alias' => Yii::t('app', 'Alias'),
            'brand' => Yii::t('app', 'Brand'),
            'variable' => Yii::t('app', 'Variable'),
            'inServiceDate' => Yii::t('app', 'In Service Date'),
            'dateOfPurchase' => Yii::t('app', 'Date Of Purchase'),
            'dateOfLastCalibration' => Yii::t('app', 'Date Of Last Calibration')
        ];
    }
    
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
    }

    public function attributesToArray() {
        throw new Exception('Not implemented');
    }
}
