<?php

//**********************************************************************************************
//                                       WSImageModel.php
//
// Author(s): Morgane Vidal
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: corresponds to the images service - extends WSModel
//***********************************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Allows the acces to the images service
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSImageModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the images service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "images");
    }
}