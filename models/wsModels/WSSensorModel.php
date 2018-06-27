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
        
        if (isset($requestRes->{WSConstants::TOKEN})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }
}