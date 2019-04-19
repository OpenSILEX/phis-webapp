<?php

//******************************************************************************
//                               WSEventModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 14 April 2019
// Contact: vincent.migot@inra.fr anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

use \openSILEX\guzzleClientPHP\WSModel;
use app\models\wsModels\WSConstants;

include_once '../config/web_services.php';

/**
 * Encapsulates the access to the provenance service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Vincent Migot <vincent.migot@inra.fr>
 */
class WSProvenanceModel extends WSModel {

    /**
     * Initializes access to the events service
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "provenances");
    }

    public function createProvenance($sessionToken, $label, $comment, $metadata) {
        $subService = "/";
        $provenance = $this->post($sessionToken, $subService, [[
            "label" => $label,
            "comment" => $comment,
            "metadata" => $metadata,
        ]]);
        
        if (
            isset($provenance->{WSConstants::METADATA}->{WSConstants::DATA_FILES})
            && count($provenance->{WSConstants::METADATA}->{WSConstants::DATA_FILES}) == 1
        ) {
            return $provenance->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
        } else {
            return $provenance;
        }
    }

}
