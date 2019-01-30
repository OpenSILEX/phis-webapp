<?php
//******************************************************************************
//                          WSAcquisitionSession.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 10 Sept, 2018
// Contact: arnaud.charleroy@inra.fr,morgane.vidal@inra.fr, anne.tireau@inra.fr,
//          pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the acquisition session service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class WSAcquisitionSession extends \openSILEX\guzzleClientPHP\WSModel {

    /**
     * initialize access to the acquisition session service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "acquisitionSessions");
    }
    
    /**
     * Finds and returns the metadata linked to a particular type
     * of acquisition session excel document
     * @param String $sessionToken connection user token
     * @param String $vectorRdfTypeUri uri of the vector type
     * @example http://www.opensilex.org/vocabulary/oeso#UAV
     * @param Array $params contains the data to send to the get service 
     * @example
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "uri" => "http://www.opensilex.org/vocabulary/oeso#UAV" 
     * ]
     * @return mixed if the acquisition metadata file exist, 
     *                  an array representing these data 
     *               else the error message 
     */
    public function getFileMetadataByURI($sessionToken, $vectorRdfTypeUri, $params) {
        $params["vectorRdfType"] = $vectorRdfTypeUri;
        $subService = "/metadataFile";
        $requestRes = $this->get($sessionToken, $subService, $params);
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
}
