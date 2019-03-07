<?php

//******************************************************************************
//                               WSEventModel.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 02 jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

use \openSILEX\guzzleClientPHP\WSModel;
use app\models\wsModels\WSConstants;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the events service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class WSEventModel extends WSModel {
    
    /**
     * Initialize access to the events service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "events");
    }
    
    
    /**
     * Get the details of an eventcorresponding to the given uri
     * 
     * @param String $sessionToken connection user token
     * @param String $uri uri of the searched event
     * @return if the event exist, an array representing it else the error message
     * eg. 
     * [
     *      uri => http://www.phenome-fppn.fr/id/event/rt001
     *      label => Test circulaire
     *      comment => 
     *      ontologiesReferences => []
     *      properties => [
     *          Object (
     *              rdfType => 
     *              rdfTypeLabels => 
     *              relation => http://www.opensilex.org/vocabulary/oeso#hasBrand
     *              relationLabels => 
     *              value => CIRC
     *              valueLabels => 
     *              domain => 
     *              labels => 
     *         ),
     *          Object (
     *              rdfType => 
     *              rdfTypeLabels => 
     *              relation => http://www.opensilex.org/vocabulary/oeso#inServiceDate
     *              relationLabels => 
     *              value => 2018-10-28
     *              valueLabels => 
     *              domain => 
     *              labels => 
     *          ),...
     *      ]
     * ]
     */
    public function getEventDetailed($sessionToken, $uri) {
        $subService = "/" . urlencode($uri);
        $eventDetailed = $this->get($sessionToken, $subService, []);

        if (isset($eventDetailed->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $eventDetailed->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $eventDetailed;
        }
    }
}