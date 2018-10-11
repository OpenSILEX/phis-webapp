<?php

//******************************************************************************
//                          WSRadiometricTargetModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 27 Sept, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the radiometric target service
 * @see \openSILEX\guzzleClientPHP\WSModel 
 * @author Migot Vincent <vincent.migot@inra.fr>
 */
class WSRadiometricTargetModel extends \openSILEX\guzzleClientPHP\WSModel {

    /**
     * initialize access to the projects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "radiometricTargets");
    }
    
    /**
     * Get the properties of a radiometric target corresponding to the given uri
     * 
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched radiometric target
     * @return if the radiometric target exist, an array representing it
     *          else the error message
     * eg. 
     * [
     *      uri => http://www.phenome-fppn.fr/id/radiometricTargets/rt001
     *      label => Test circulaire
     *      comment => 
     *      ontologiesReferences => []
     *      properties => [
     *          Object (
     *              rdfType => 
     *              rdfTypeLabels => 
     *              relation => http://www.phenome-fppn.fr/vocabulary/2017#hasBrand
     *              relationLabels => 
     *              value => CIRC
     *              valueLabels => 
     *              domain => 
     *              labels => 
     *         ),
     *          Object (
     *              rdfType => 
     *              rdfTypeLabels => 
     *              relation => http://www.phenome-fppn.fr/vocabulary/2017#inServiceDate
     *              relationLabels => 
     *              value => 6-07-10
     *              valueLabels => 
     *              domain => 
     *              labels => 
     *          ),...
     *      ]
     * ]
     */
    public function getDetails($sessionToken, $uri) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, []);

        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }

}
