<?php

//******************************************************************************
//                                       WSSensorModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 29 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  29 mars 2018
// Subject: Corresponds to the sensors service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the sensors service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSSensorModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the sensors service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "sensors");
    }
    
    /**
     * 
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched sensor
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "uri" => "http://uri/of/my/entity" 
     * ]
     * @return mixed if the sensor exist, an array representing the sensor 
     *               else the error message 
     */
    public function getSensorByUri($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        $data = $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        if (isset($data) && is_array($data) && count($data) > 0)  {
            return (array) $data[0];
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched sensor profile
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "uri" => "http://uri/of/my/entity" 
     * ]
     * @return mixed if the sensor exist and has a profile, an array representing the sensor profile
     *               else the error message 
     */
    public function getSensorProfile($sessionToken, $uri, $params) {
        $subService = "/profiles/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param array $params data corresponding to the sensor profile
     * @return mixed the query result 
     *               a string "token" if token expired
     */
    public function postSensorProfile($sessionToken, $params) {
        $subService = "/profiles";
        $requestRes = $this->post($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::TOKEN_INVALID})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }
    
    /**
     * Call the webservice to update the list of measured variable by the given sensor
     * @param string $sessionToken
     * @param string $sensorUri
     * @param array $variablesUri
     * @return mixed the query result 
     *           a string "token" if token expired
     */
    public function putSensorVariables($sessionToken, $sensorUri, $variablesUri) {
        $subService = "/" . urlencode($sensorUri) . "/variables";
        $requestRes = $this->put($sessionToken, $subService, $variablesUri);

        if (isset($requestRes->{WSConstants::TOKEN_INVALID})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }
    
     /**
     * Return the latest measured value for a variable and a sensor
     * @param string $sessionToken
     * @param string $provenanceUri
     * @param string $variableUri
     * @return mixed data or the error message 
     */
    public function getLastSensorVariableData($sessionToken,$variableUri, $sensorUri) {
        // Define the parameter array
        $params = [
            "pageSize" => 1,
            "page" => 0,
            "variable" => $variableUri,
            "dateSortAsc" => "false"
        ];
        $subService = "/" . urlencode($sensorUri) . "/data";
        // Send the request
        $requestRes = $this->get($sessionToken, $subService, $params);
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
        ];
        $subService = "/" . urlencode($sensorUri) . "/data";
        
        if ($startDate != null) {
            $params["startDate"] = $startDate->format(WSDataModel::DATE_FORMAT);
        }

        if ($endDate != null) {
            $params["endDate"] = $endDate->format(WSDataModel::DATE_FORMAT);
        }
        
        $params["dateSortAsc"] = "true";
        
        // Send the request
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        // Return the result data
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
}