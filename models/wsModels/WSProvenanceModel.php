<?php

//******************************************************************************
//                               WSProvenanceModel.php
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

    /**
     * Create provenance with label, comment and metadata
     * @param type $sessionToken
     * @param type $label
     * @param type $comment
     * @param type $metadata
     * @return type
     */
    public function createProvenance($sessionToken, $label, $comment, $metadata) {
        $subService = "/";
        $provenance = $this->post($sessionToken, $subService, [[
        "label" => $label,
        "comment" => $comment,
        "metadata" => $metadata,
        ]]);

        if (
                isset($provenance->{WSConstants::METADATA}->{WSConstants::DATA_FILES}) && count($provenance->{WSConstants::METADATA}->{WSConstants::DATA_FILES}) == 1
        ) {
            return $provenance->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
        } else {
            return $provenance;
        }
    }

    /**
     * Return an array of existing provenances match with criteria
     * 
     * @param string $sessionToken
     * @param array $userCriteria array of citeria
     * @return type
     */
    public function getSpecificProvenancesByCriteria($sessionToken,$userCriteria = []) {
        $pageSize = 200;
        $subService = "/";
        $params = array_merge($userCriteria,[
            WSConstants::PAGE => 0,
            WSConstants::PAGE_SIZE => $pageSize
        ]);
       
        $provenanceResult = $this->get($sessionToken, $subService, $params);
        
        if (isset($provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}) && isset($provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT}) && $provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT} > 0) {

            $result = $provenanceResult->{WSConstants::RESULT}->{WSConstants::DATA};

            $totalPages = $provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_PAGES};

            for ($currentPage = 1; $currentPage < $totalPages; $currentPage++) {
                $params[WSConstants::PAGE] = $currentPage;
                $params[WSConstants::PAGE_SIZE] = $pageSize;
                
                $provenanceResult = $this->get($sessionToken, $subService, $params);
                
                $result = array_merge($result, $provenanceResult->{WSConstants::RESULT}->{WSConstants::DATA});
            }
            
            return $result;
        }elseif(isset($provenanceResult->{WSConstants::RESULT}->{WSConstants::DATA})){
            return  $provenanceResult->{WSConstants::RESULT}->{WSConstants::DATA};
        }else{
            return [];
        }
    }
    
    /**
     * Return an array of all existing provenances
     * 
     * @param type $sessionToken
     * @return type
     */
    public function getAllProvenances($sessionToken) {
        $pageSize = 200;
        $subService = "/";
        $provenanceResult = $this->get($sessionToken, $subService, [
            WSConstants::PAGE => 0,
            WSConstants::PAGE_SIZE => $pageSize
        ]);

        if (isset($provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}) && isset($provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT}) && $provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT} > 0) {

            $result = $provenanceResult->{WSConstants::RESULT}->{WSConstants::DATA};

            $totalPages = $provenanceResult->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_PAGES};

            for ($currentPage = 1; $currentPage < $totalPages; $currentPage++) {
                $provenanceResult = $this->get($sessionToken, $subService, [
                    WSConstants::PAGE => $currentPage,
                    WSConstants::PAGE_SIZE => $pageSize
                ]);
                
                $result = array_merge($result, $provenanceResult->{WSConstants::RESULT}->{WSConstants::DATA});
            }
            
            return $result;
        } else {
            return [];
        }
    }

}
