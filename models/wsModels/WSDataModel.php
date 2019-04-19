<?php

//******************************************************************************
//                                       WSDataModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the data service
 * @see \openSILEX\guzzleClientPHP\WSModel 
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSDataModel extends\openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the projects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "data");
    }
    
    public function postData($token, $values) {
        $subService = "/";
        $body = json_encode($values, $options = JSON_UNESCAPED_SLASHES);
        $dataResult = $this->post($token, $subService, $values);

        return $dataResult;
    }
}