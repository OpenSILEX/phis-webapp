<?php

//**********************************************************************************************
//                                       OntologyReference.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 24 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 24 2017
// Subject: The model corresponding to the references to others ontologies in the triplestore
//***********************************************************************************************

namespace app\models;

class OntologyReference {
    public $property;
    public $object;
    public $seeAlso;
    
    public function arrayToAttributes($array) {
        $this->property = $array->{"property"};
        $this->object = $array->{"object"};
        $this->seeAlso = explode("\"", $array->{"seeAlso"})[1];
    }
    
    public function attributesToArray() {
        return [
          "property" => $this->property,
          "object" => $this->object,
          "seeAlso" => $this->seeAlso
        ];
    }
}
