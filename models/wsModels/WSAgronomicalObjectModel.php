<?php

//**********************************************************************************************
//                                       WSAgronomicalObjectModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: Corresponds to the agronomical object service. extends WSModel
//***********************************************************************************************
//
namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the agronomical objects service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSAgronomicalObjectModel extends \openSILEX\guzzleClientPHP\WSModel {
    /**
     * initialize access to the agronomical objects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "agronomicalObjects");
    }
}
