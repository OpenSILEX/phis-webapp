<?php

//******************************************************************************
//                        DeviceDataSearch.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 8th November 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;
use Yii;

/**
 * implements the search action for the sensor data
 * @author Vincent Migot <vincent.migot@inra.fr>
 * @update [Morgane Vidal] 19 April, 2019 : rename to device to deal with others devices with data.
 */
class DeviceDataSearch extends \yii\base\Model {
    
    /**
     * start date of the searched data
     * @var string
     */
    public $dateStart;
    /**
     * end date of the searched data
     * @var string 
     */
    public $dateEnd;
    /**
     * device uri of the searched data
     * @var string
     */
    public $sensorURI;
    
    /**
     * variable uri of the searched data
     * @var string
     */
    public $variableURI;
        
    /**
     * graph name of the searched data
     * @var string
     */
    public $graphName;
    
    /**
     * Store provenance data to prevent multiple calls to WS
     */
    const SESSION_PROVENANCES = 'store_provenances_infos';
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dateStart', 'dateEnd'], 'safe'],
            [['sensorURI', 'variableURI', 'graphName'], 'string']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
          'dateStart' =>  Yii::t('app', 'Date Start'),
          'dateEnd' => Yii::t('app', 'Date End'),
        ];
    }
    
    /**
     * Return environment data corresponding to the current parameters
     * The result array contains both parameters and result data for convenience
     * @param string $sessionToken
     * @return array
     * @example
     *  [
     *      'graphName' => 'TRAIT_METHOD_UNIT' (length=17)
     *      'variableUri' => 'http://www.phenome-fppn.fr/id/variables/v001' (length=44)
     *      'sensorUri' => 'http://www.phenome-fppn.fr/m3p/eo/2016/sa1600009' (length=48)
     *      'dateStart' => object(DateTime) 2017-06-09 10:56:00+2000
     *      'dateEnd' => object(DateTime) 2017-06-16 10:56:00+2000
     *      'data' => [
     *          [
     *              sensorUri => 'http://www.phenome-fppn.fr/m3p/eo/2016/sa1600009'
     *              date => '2017-06-15T10:51:00+0200'
     *              value => 1.29
     *          ]
     *      ]
     *  ]
     */
    public function getEnvironmentData($sessionToken) {
        $ws = new WSEnvironmentModel();
        
        // Define start and end time period
        $dateTimeStart = null;
        $dateTimeEnd = null;
        
        if ($this->dateStart == null && $this->dateEnd == null) {
            // If no dates are defined get the last data for this sensor and variable
            $lastData = $ws->getLastSensorVariableData($sessionToken, $this->sensorURI, $this->variableURI);
            // If no dates are defined get the last data for this sensor and variable
            
            // Get the last date if exists
            $lastDate = null;
            if ($lastData['date']) {
                $lastDate = $lastData["date"];
            }
            // If last date found, compute the latest week period
            if ($lastDate != null) {
                $dateTimeEnd = new \DateTime($lastDate);
                //SILEX:info
                // @see php.net/manual/en/dateinterval.construct.php
                // create start date from last date
                $dateTimeStart = new \DateTime($lastDate);
                // substract a 'P'eriode of '7' 'D'ays to the date
                $dateTimeStart->sub(new \DateInterval("P7D"));
                //\SILEX:info
            } else {
                return null;
            }

        } else if ($this->dateStart == null) {
            // If only dateEnd is defined
            $dateTimeEnd = new \DateTime($this->dateEnd);
        } else if ($this->dateEnd == null) {
            // If only dateStart is defined
            $dateTimeStart = new \DateTime($this->dateStart);
        } else {
            // Both dateStart and dateAnd are defined
            $dateTimeStart = new \DateTime($this->dateStart);
            $dateTimeEnd = new \DateTime($this->dateEnd);
        }

        // Get all data
        $data = $ws->getAllSensorData($sessionToken, $this->sensorURI, $this->variableURI, $dateTimeStart, $dateTimeEnd);
       
        // Construct result
        $result = [
            "graphName" => $this->graphName,
            "variableUri" => $this->variableURI,
            "sensorUri" => $this->sensorURI,
            "dateStart" => $dateTimeStart,
            "dateEnd" => $dateTimeEnd,
            "data" => $data
        ];
        
        return $result;
    }
}
