<?php

//******************************************************************************
//                                       WSUriModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 12 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  12 mars 2018
// Subject: Corresponds to the uri service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the uri service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class WSUriModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * Initialize access to the traits service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "uri");
    }
    
    /**
     * 
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom descendants are wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000" 
     * ]
     * @return mixed if the given uri exist (descendants or empty list)
     *               else the error message 
     */
    public function getDescendants($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri) . "/descendants";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            $toReturn[WSConstants::PAGINATION] = (array) $requestRes->{WSConstants::METADATA}->{WSConstants::PAGINATION};
            $toReturn[WSConstants::DATA] =  (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
            return $toReturn;
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom siblings are wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return mixed if the given uri exist (siblings or empty list)
     *               else the error message 
     */
    public function getSiblings($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri) . "/siblings";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom ancestors are wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return mixed if the given uri exist (ancestors or empty list)
     *               else the error message 
     */
    public function getAncestors($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri) . "/ancestors";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom instances are wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return mixed if the given uri exist (instances or empty list)
     *               else the error message 
     */
    public function getInstances($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri) . "/instances";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom type is wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return boolean if the uri exists or not
     *         mixed web service error
     */
    public function exist($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri) . "/exist";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0]->{'exist'};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * Ask to the web service the rdf type of the given uri
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom type is wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return string The rdfType of the given uri
     */
    public function getUriType($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri) . "/type";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0]->{'rdfType'};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @param String $sessionToken Connection user token
     * @param String $uri URI of the concept whom type is wanted
     * @param Array $params Contains the data to send to the get service 
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000"
     * ]
     * @return mixed The given uri data if it exist
     *         mixed Error (Unknown, web service error, ...)
     */
    public function getByUri($sessionToken, $uri, $params) {
        $subService = "/" . urlencode($uri);
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA}[0];
        } else {
            return $requestRes;
        }
    }
}