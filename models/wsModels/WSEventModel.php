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
 * Encapsulates the access to the events service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class WSEventModel extends WSModel {
    
    /**
     * Initializes access to the events service
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "events");
    }
    
    /**
     * Gets the details of an event corresponding to the given URI
     * @param String $sessionToken connection user token
     * @param String $uri URI of the searched event
     * @return if the event exist, an array representing it else the error message
     * @example 
     * [
     *     "annotations": [
     *         {
     *           "uri": "http://www.phenome-fppn.fr/test/id/annotation/e9cb3b9b-bb50-49e2-8a74-40a2bfda18d1",
     *           "creationDate": "2019-03-07T15:23:52+01:00",
     *           "creator": "http://www.phenome-fppn.fr/diaphen/id/agent/admin_phis",
     *           "motivatedBy": "http://www.w3.org/ns/oa#describing",
     *           "comments": [
     *             "The displacement was fast"
     *           ],
     *           "targets": [
     *             "http://www.phenome-fppn.fr/test/id/event/c07ea114-e1ef-4341-8b8e-3b3e58b01852"
     *           ]
     *         }
     *       ],
     *       "uri": "http://www.phenome-fppn.fr/test/id/event/c07ea114-e1ef-4341-8b8e-3b3e58b01852",
     *       "type": "http://www.opensilex.org/vocabulary/oeev#MoveFrom",
     *       "concernedItems": [
     *         {
     *           "labels": [
     *             "sensor 1"
     *           ],
     *           "uri": "http://www.phenome-fppn.fr/mtpvm/2018/s18001",
     *           "typeURI": "http://www.opensilex.org/vocabulary/oeso#LiDAR"
     *         },
     *         {
     *           "labels": [
     *             "sensor 6"
     *           ],
     *           "uri": "http://www.phenome-fppn.fr/mtpvm/2019/s19006",
     *           "typeURI": "http://www.opensilex.org/vocabulary/oeso#LiDAR"
     *         }
     *       ],
     *       "date": "2017-09-08T12:00:00+01:00",
     *       "properties": [
     *         {
     *           "rdfType": null,
     *           "relation": "http://www.opensilex.org/vocabulary/oeev#from",
     *           "value": "Space"
     *         }
     *       ]
     *     }
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
