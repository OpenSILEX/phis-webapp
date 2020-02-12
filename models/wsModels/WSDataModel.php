<?php

//******************************************************************************
//                                       WSDataModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the data service
 * @see \openSILEX\guzzleClientPHP\WSModel 
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSDataModel extends\openSILEX\guzzleClientPHP\WSModel {
    
     /**
     * Date format for the webservice filters with timezone
     * @example: 2017-05-03T12:35:00+2000
     */
    const DATE_FORMAT = "Y-m-d\TH:i:sO";
    
    /**
     * initialize access to the projects service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "data");
    }
    
    /**
     * Get data by calling the search service with the given search parameters.
     * Search data by variableUri (required), startDate, endDate, objectUri, 
     * objectLabel, provenanceUri, provenanceLabel, dateSortAsc
     * @param string $sessionToken
     * @param string $variableUri 
     * @param string $startDate
     * @param string $endDate
     * @param string $objectUri
     * @param string $objectLabel
     * @param string $provenanceUri
     * @param string $provenanceLabel
     * @param string $dateSortAsc
     * @return mixed the query result
     */
    public function getDataSearch($sessionToken, $params) {
        $subService = "/search";
        
        return $this->get($sessionToken, $subService, $params);
    }
}