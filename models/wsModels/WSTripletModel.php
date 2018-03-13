<?php
//******************************************************************************
//                                       WSTripletModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: Corresponds to the triplets service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the triplets service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSTripletModel extends \openSILEX\guzzleClientPHP\WSModel {
    /**
     * initialize access to the traits service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "triplets");
    }
}
