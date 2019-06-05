<?php

//******************************************************************************
//                                       WSActuatorModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the actuators service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSActuatorModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the sensors service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "actuators");
    }
    
    /**
     * 
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched actuator
     * @param Array $params contains the data to send to the get service 
     * @example
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "uri" => "http://uri/of/my/entity" 
     * ]
     * @return mixed if the actuator exist, an array representing the actuator 
     *               else the error message 
     */
    public function getActuatorByUri($sessionToken, $uri, $params) {
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
     * Call the webservice to update the list of measured variable by the given actuator
     * @param string $sessionToken
     * @param string $actuatorUri
     * @param array $variablesUri
     * @return mixed the query result 
     *           a string "token" if token expired
     */
    public function putSensorVariables($sessionToken, $actuatorUri, $variablesUri) {
        $subService = "/" . urlencode($actuatorUri) . "/variables";
        $requestRes = $this->put($sessionToken, $subService, $variablesUri);

        if (isset($requestRes->{WSConstants::TOKEN})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }
}