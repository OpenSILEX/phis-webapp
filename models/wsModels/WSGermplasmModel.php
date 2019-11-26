<?php

//******************************************************************************
//                                       WSGermplasmModel.php
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 08 2019
// Subject: Corresponds to the germplasm service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the germplasm service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Alice Boizet <alice.boizet@inra.fr>
 */

class WSGermplasmModel extends \openSILEX\guzzleClientPHP\WSModel {
    /**
     * initialize access to the germplasm service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "germplasm");
    }

}