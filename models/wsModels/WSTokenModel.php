<?php

//**********************************************************************************************
//                                       WSTokenModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  February, 2017
// Subject: access to the web service to manage connection tokens
//***********************************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';
include_once '../models/wsModels/WSConstants.php';

/**
 * Encapsulate the access to the token service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSTokenModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the token service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "brapi/v1/token");
    }
    
    /**
     * get the user session token
     * @param string $username
     * @param string $password
     * @param string $client_id
     * @return mixed if the user's login/password is ok, the session token
     *               else null
     */
    public function getToken($username, $password, $client_id = null) {
        $bodyRequest["grant_type"] = "password";
        $bodyRequest["username"] = $username;
        $bodyRequest["password"] = $password;
        if ($client_id !== null && $client_id !== "") {
            $bodyRequest["client_id"] = $client_id;
        }
        $bodyToSend = $bodyRequest;
        $requestRes = $this->post("", "", $bodyToSend);

        if (isset($requestRes->{WSConstants::ACCESS_TOKEN})) {
                        
            // Compute token expiration timestamp
            $delay = $requestRes->{WSConstants::TOKEN_EXPIRES_IN};
            $date = new \DateTime();
            $date->add(new \DateInterval('PT' . $delay . 'S'));
            $tokenTimeout = $date->getTimestamp();

            // set cookie storing token timeout
            setcookie(
                WSConstants::TOKEN_COOKIE_TIMEOUT,
                $tokenTimeout,
                time() + $delay,
                '/'
            );
            
           return $requestRes->{WSConstants::ACCESS_TOKEN};
        } else {
            return null;
        }
    }
}
