<?php

//******************************************************************************
//                                       WSVectorModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject: Corresponds to the vectors service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the vectors service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSVectorModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the vectors service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "vectors");
    }
    
    /**
     * 
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched vector
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "uri" => "http://uri/of/my/entity" 
     * ]
     * @return mixed if the vector exist, an array representing the vector 
     *               else the error message 
     */
    public function getVectorByUri($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }
}