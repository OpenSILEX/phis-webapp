<?php

//******************************************************************************
//                                       YiiShootingConfigurationModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: the yii model for the shooting configuration (image for example).
//          Used with web services
//******************************************************************************

namespace app\models\yiiModels;

/**
 * The model for the shooting configuration (of an image for example)
 *
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiShootingConfigurationModel extends \app\models\wsModels\WSActiveRecord {
    
    /**
     * the date of the view price with timezone.
     * expected format : yyyy-MM-dd HH:mm:ssZ
     *  (e.g 2017-06-15 10:51:00+0200)
     * @var string
     */
    public $date;
    const DATE = "date";
    /**
     * the timestamp of the date. 
     *  (e.g 1513347218282)
     * @var string
     */
    public $timestamp;
    const TIMESTAMP = "timestamp";
    /**
     * the sensor position went during the view price. 
     *  (e.g 2)
     * @var string
     */
    public $sensorPosition;
    const SENSOR_POSITION = "sensorPosition";
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['date', 'timestamp', 'sensorPosition'], 'safe']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'date' => Yii::t('app', 'Date'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'sensorPosition' => Yii::t('app', 'Sensor Position')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the concerned item
     */
    protected function arrayToAttributes($array) {
        $this->date = $array[YiiShootingConfigurationModel::DATE];
        $this->timestamp = $array[YiiShootingConfigurationModel::TIMESTAMP];
        $this->sensorPosition = $array[YiiShootingConfigurationModel::SENSOR_POSITION];
    }

    /**
     * Create an array representing the shooting configuration
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $toReturn[YiiShootingConfigurationModel::DATE] = $this->date;
        $toReturn[YiiShootingConfigurationModel::TIMESTAMP] = $this->timestamp;
        $toReturn[YiiShootingConfigurationModel::SENSOR_POSITION]= $this->sensorPosition;
        
        return $toReturn;
    }
}
