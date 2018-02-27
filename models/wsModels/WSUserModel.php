<?php

//**********************************************************************************************
//                                       WSUserModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: Corresponds to the users service - extends WSModel
//***********************************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the users service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSUserModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the users service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "users");
    }
    
    /**
     * 
     * @param String $sessionToken connection user token
     * @param String $email email of the searched user
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "email" => "user[at]email.com" 
     * ]
     * @return mixed if the user exist, an array representing the user 
     *               else the error message 
     */
    public function getUserByEmail($sessionToken, $email, $params) {
        $subService = "/" . urlencode($email);
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }
}
