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
use app\models\yiiModels\YiiEventModel;

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
     * Gets the event corresponding to the given URI
     * @param String $sessionToken connection user token
     * @param String $uri URI of the searched event
     * @return if the event exists, an array representing it else the error message
     * @example 
     * [
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
    public function getEvent($sessionToken, $uri) {
        $subService = "/" . urlencode($uri);
        $event = $this->get($sessionToken, $subService, []);

        if (isset($event->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return (array) $event->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $event;
        }
    }
    
    /**
     * Gets the event's annotations
     * @param String $sessionToken connection user token
     * @param String $uri URI of the event
     * @return if the event exists, an array representing the annotations
     *  else the error message
     * @example 
     * [
     *   {
     *     "creator": "http://www.phenome-fppn.fr/diaphen/id/agent/marie_dupond",
     *     "motivatedBy": "http://www.w3.org/ns/oa#commenting",
     *     "bodyValues": [
     *       "string"
     *     ],
     *     "targets": [
     *       "string"
     *     ],
     *     "uri": "string",
     *     "creationDate": "string"
     *   }
     * ]
     */
    public function getEventAnnotations($sessionToken, $params) {
        $subService = "/" . urlencode($params[YiiEventModel::URI]) . "/annotations";
        $response = $this->get($sessionToken, $subService, $params);

        if (isset($response->{WSConstants::RESULT}->{WSConstants::DATA})) {
            return $response->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $response;
        }
    }
}
