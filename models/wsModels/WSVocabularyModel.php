<?php

//******************************************************************************
//                                       WSVocabularyModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>, Arnaud Charleroy <arnaud.charleroy@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 july 2018
// Contact:  morgane.vidal@inra.fr,arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 july 2018
// Subject: Corresponds to the vocabularies service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the vocabulary properties service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr
 */
class WSVocabularyModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the vocabulary properties service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "vocabularies");
    }
    
     /**
     * 
     * @param string $sessionToken
     * @param array $params array with page, pageSize and rdfType
     * @return mixed if the triplestore has namespaces there will be send  
     */
    public function getNamespaces($sessionToken, $params) {
        $subService = "/namespaces";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param array $params array with page, pageSize and rdfType
     * @return mixed if the rdfType has contact properties, an array with all the properties
     *               else the error message (can be no result found)
     */
    public function getContactsProperties($sessionToken, $params) {
        $subService = "/contacts/properties";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }

    /**
     * 
     * @param string $sessionToken
     * @param array $params array with page, pageSize and rdfType
     * @return mixed if the rdfType is a device and has device properties, an array with all the properties
     *               else the error message (can be no result found)
     */
    public function getDevicesProperties($sessionToken, $params) {
        $subService = "/devices/properties";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param array $params array with page, pageSize and rdfType
     * @return mixed the rdfs properties
     *               if an error occurred, the error message
     */
    public function getRdfsProperties($sessionToken) {
       $subService = "/rdfs/properties";
        $requestRes = $this->get($sessionToken, $subService, null);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        } 
    }
}