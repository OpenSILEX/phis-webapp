<?php

//**********************************************************************************************
//                                       WSGroupModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: Corresponds to the groups service - extends WSModel
//***********************************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the groups service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSGroupModel extends \openSILEX\guzzleClientPHP\WSModel {
    /**
     * initialize access to the groups service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "groups");
    }
    
     /**
     * 
     * @param String $sessionToken connection user token
     * @param String $name uri of the searched experiment
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "name" => "groupName" 
     * ]
     * @return mixed if the group exist, an array representing the group 
     *               else the error message 
     */
    public function getGroupByName($sessionToken, $name, $params) {
        $subService = "/" . urlencode($name);
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }
}
