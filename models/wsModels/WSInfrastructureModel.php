<?php

//******************************************************************************
//                                       WSInfrastructureModel.java
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 10 sept. 2018
// Contact: vincent.migot@inra.fr anne.tireau@inra.fr, pascal.neveu@inra.fr
// Subject: represnet infrastructures API
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the infrastructure service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Vincent Migot <vincent.migot@inra.fr>
 */
class WSInfrastructureModel extends \openSILEX\guzzleClientPHP\WSModel {

    /**
     * initialize access to the infrastructures service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "infrastructures");
    }

    /**
     * Return list of infrastructures corresponding to given params
     * @param string $sessionToken connection user token
     * @param string $uri uri of the searched infrastructure
     * @param array $params contains the data to send to the get service 
     * @return type
     */
    public function getInfrastructures($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, $params);

        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }

    /**
     * Return infrastructures details corresponding to given params
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched infrastructure
     * @param Array $params contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     * ]
     * @return mixed if the infrastructure exist, an array representing 
     *                the infrastructure details else the error message 
     */
    public function getInfrastructureDetails($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, $params);

        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }

}
