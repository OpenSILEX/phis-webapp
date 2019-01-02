<?php

//******************************************************************************
//                                       WSEventModel.php
//
// Author(s): Andréas Garcia <andreas.garcia@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 02 janvier 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the events service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class WSEventModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the events service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "events");
    }
}