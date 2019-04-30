<?php

//**********************************************************************************************
//                                       WSScientificObjectModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: Corresponds to the scientific object service. extends WSModel
//***********************************************************************************************
//
namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the scientific objects service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSScientificObjectModel extends \openSILEX\guzzleClientPHP\WSModel {
    /**
     * initialize access to the scientific objects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "scientificObjects");
    }
    
    /**
     * Update the metadata of a given scientific object in the context of a given experiment.
     * @param string $sessionToken
     * @param string $uri
     * @param string $experiment
     * @param array $params
     * @return mixed the update result.
     */    
    public function putByExperiment($sessionToken, $uri, $experiment, $params) {
        //1. Generate request path
        $subservice = "/" . urlencode($uri) . "/" . urlencode($experiment);
        
        //2. call the put service
        $requestRes;
        try {
            $requestRes = $this->put($sessionToken, $subservice, $params);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $requestRes = json_decode($e->getResponse()->getBody()->getContents());
        }
        return $requestRes;
    }
}
