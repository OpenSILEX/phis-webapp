<?php

//******************************************************************************
//                                       WSSpeciesModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 déc. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulates the access to the species service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSSpeciesModel extends \openSILEX\guzzleClientPHP\WSModel {
    /**
     * initialize access to the species service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "species");
    }
}