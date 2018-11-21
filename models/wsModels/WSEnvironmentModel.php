<?php
//******************************************************************************
//                           WSEnvironmentModel.java
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 14th, November 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the environment data service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Vincent Migot <vincent.migot@inra.fr>
 */
class WSEnvironmentModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    const DATE_FORMAT = "Y-m-d\TH:i:sO";
    /**
     * initialize access to the environments service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "environments");
    }
    
    /**
     * Return the latest measured value for a variable and a sensor
     * @param string $sessionToken
     * @param string $variableUri
     * @param string $sensorUri
     * @return mixed data or the error message 
     */
    public function getLastSensorVariableData($sessionToken, $sensorUri, $variableUri) {
        // Define the parameter array
        $params = [
            "pageSize" => 1,
            "page" => 0,
            "variable" => $variableUri,
            "sensor" => $sensorUri,
            "dateSortAsc" => "false"
        ];
        
        // Send the request
        $requestRes = $this->get($sessionToken, null, $params);
        
        // Return the first result data or null
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            if (count($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}) > 0) {
                return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
            } else {
                return null;
            }
            
        } else {
            return $requestRes;
        }
    }

    //SILEX:todo
    //The page size should not be fixed to 80000 but this method should call multiple times
    //the web service in order to really get all the results
    //\SILEX:todo
    /**
     * Return the 80000 first measured variable corresponding to the given parameters
     * @param string $sessionToken
     * @param string $sensorUri
     * @param string $variableUri
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed data or the error message 
     */
    public function getAllSensorData($sessionToken, $sensorUri, $variableUri, $startDate, $endDate) {
        // Define the parameter array
        $params = [
            "pageSize" => 80000,
            "page" => 0,
            "variable" => $variableUri,
            "sensor" => $sensorUri,
        ];
        
        if ($startDate != null) {
            $params["startDate"] = $startDate->format(self::DATE_FORMAT);
        }

        if ($endDate != null) {
            $params["endDate"] = $endDate->format(self::DATE_FORMAT);
        }
        
        $params["dateSortAsc"] = "true";
        
        // Send the request
        $requestRes = $this->get($sessionToken, null, $params);
        
        // Return the result data
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
}
