<?php

//******************************************************************************
//                                       WSDataAnalysisModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 29 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  29 mars 2018
// Subject: Corresponds to the dataAnalysis service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the dataAnalysis service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSDataAnalysisModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the dataAnalysis service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "dataAnalysis");
    }
    
  
    /**
     * 
     * @param String $sessionToken connection user token
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return mixed if the sensor exist and has a profile, an array representing the sensor profile
     *               else the error message 
     * 
     * [
     *   {
     *    "id": "6396be5c21395c59a278f09731b4176c",
     *    "documentUri": "http://www.opensilex.org/sunagri/documents/documentaff1f589c1684e6fa814a2e331c3631e",
     *    "extractDockerFilesState": true,
     *    "display_name": "Shiny App Test 5",
     *    "description": "",
     *    "container_cmd": null,
     *    "container_image": "opensilex/shinyproxy-6396be5c21395c59a278f09731b4176c",
     *    "env_variables": {},
     *    "application_url": "http://0.0.0.0:8085/app/6396be5c21395c59a278f09731b4176c"
     *   }
     * ]
     */
    public function getApplications($sessionToken, $params) {
        $subService = "/applications";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken connection user token
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return mixed if the sensor exist and has a profile, an array representing the sensor profile
     *               else the error message 
     * 
     *   "status": [
     *     {
     *       "message": "Info",
     *       "exception": {
     *         "type": "Running",
     *         "href": null,
     *         "details": null
     *       }
     *     }
     *    ]
     */
    public function getShinyServerStatus($sessionToken, $params) {
        $subService = "/shinyServerStatus";
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
     * @param array $params data corresponding to the r function
     * * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "packageName" => "stats",
     *  "functionName" => "rnorm",
     *  "jsonParameters" => "{'n' => 1000}"
     * ]
     * @return mixed the query result 
     *               a string "token" if token expired
     */
    public function postRCall($sessionToken, $params) {
        $subService = "/R";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::TOKEN_INVALID})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }

    protected function arrayToAttributes($array) {
        
    }

}