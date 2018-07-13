<?php

//******************************************************************************
//                                       WSVocabulary.php
//
// Author(s): Arnaud Charleroy <arnaud.charleroy@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 july 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 july 2018
// Subject: Corresponds to the vocabularies service - extends WSModel
//******************************************************************************

namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Encapsulate the access to the vocabularies service
 * @see \openSILEX\guzzleClientPHP\WSModel
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class WSVocabularyModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the vocabularies service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "vocabularies");
    }
   
    public function getNamespaces($sessionToken, $params) {
        $subService = "/namespaces";
        $requestRes = $this->get($sessionToken, $subService, $params);
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array) $requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
        } else {
            return $requestRes;
        }
    }
}