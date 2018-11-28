<?php

//**********************************************************************************************
//                                       YiiInstanceDefinition.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 24 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 24 2017
// Subject: The model corresponding to the basic format of some models (e.g. Trait, Method)
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\OntologyReference;

/**
 * The yii model for the instance definition. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSActiveRecord
 * @see app\models\OntologyReference
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiInstanceDefinitionModel extends \app\models\wsModels\WSActiveRecord {
    /**
     * the instance definition's uri
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * The rdf type of the instance definition
     *  (e.g. http://www.phenome-fppn.fr/vocabulary/2017#Thermocouple)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    /**
     * the instance definition's label
     * @var string
     */
    public $label;
    const LABEL = "label";
    /**
     * the instance definition's comment
     * @var string
     */
    public $comment;
    const COMMENT = "comment";
    /**
     * the list of the ontologies references for the instance definition object
     * @var array<OntologyReference>
     */
    public $ontologiesReferences;
    const ONTOLOGIES_REFERENCES = "ontologiesReferences";
    const OBJECT = "object";
    const PROPERTY = "property";
    const SEE_ALSO = "seeAlso";
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['ontologiesReferences', 'label', 'uri'], 'safe'],
            [['comment'], 'string']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'uri' => 'URI',
            'label' => \Yii::t('app', 'Internal Label'),
            'comment' => \Yii::t('app', 'Comment'),
            'ontologiesReferences' => \Yii::t('app', 'Related References')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     an instance definition
     */
    protected function arrayToAttributes($array) {
        if (is_array($array)) {
            $this->uri = $array[YiiInstanceDefinitionModel::URI];
            $this->label = $array[YiiInstanceDefinitionModel::LABEL];
            $this->comment = $array[YiiInstanceDefinitionModel::COMMENT];

            if (isset($array[YiiInstanceDefinitionModel::ONTOLOGIES_REFERENCES])) {
                $this->ontologiesReferences = null;
                foreach ($array[YiiInstanceDefinitionModel::ONTOLOGIES_REFERENCES] as $ontologyReference) {
                    $ontoRef = new OntologyReference();
                    $ontoRef->arrayToAttributes($ontologyReference);
                    $this->ontologiesReferences[] = $ontoRef;
                }
            }
        } else {
            $this->uri = $array->{YiiInstanceDefinitionModel::URI};
            $this->label = $array->{YiiInstanceDefinitionModel::LABEL};
            $this->comment = $array->{YiiInstanceDefinitionModel::COMMENT};
            
            if (isset($array->{YiiInstanceDefinitionModel::ONTOLOGIES_REFERENCES})) {
                $this->ontologiesReferences = null;
                foreach ($array->{YiiInstanceDefinitionModel::ONTOLOGIES_REFERENCES} as $ontologyReference) {
                    $ontoRef = new OntologyReference();
                    $ontoRef->arrayToAttributes($ontologyReference);
                    $this->ontologiesReferences[] = $ontoRef;
                }
            }
        }
    }
    /**
     * Create an array representing the instance definition
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() { 
        $toReturn = parent::attributesToArray();
        $toReturn[YiiInstanceDefinitionModel::LABEL] = $this->label;
        
        if (isset($this->comment) 
                && $this->comment !== null
                && $this->comment !== "") {
            $toReturn[YiiInstanceDefinitionModel::COMMENT] = $this->comment;
        }
        
        if (isset($this->ontologiesReferences) && $this->ontologiesReferences !== null) {
            foreach($this->ontologiesReferences as $ontologyReference) {
                if ($ontologyReference[YiiInstanceDefinitionModel::OBJECT] !== null 
                        && $ontologyReference[YiiInstanceDefinitionModel::OBJECT] !== "") {
                    if (!is_array($ontologyReference)) {
                        $toReturn[YiiInstanceDefinitionModel::ONTOLOGIES_REFERENCES][] = $ontologyReference->attributesToArray();
                    } else {
                        $ontoRef[YiiInstanceDefinitionModel::PROPERTY] = $ontologyReference[YiiInstanceDefinitionModel::PROPERTY];
                        $ontoRef[YiiInstanceDefinitionModel::OBJECT] = $ontologyReference[YiiInstanceDefinitionModel::OBJECT];
                        $ontoRef[YiiInstanceDefinitionModel::SEE_ALSO] = $ontologyReference[YiiInstanceDefinitionModel::SEE_ALSO];
                        $toReturn[YiiInstanceDefinitionModel::ONTOLOGIES_REFERENCES][] = $ontoRef;
                    }
                 }
            }
        }
        
        return $toReturn;
    }
    
    /**
     * get list of relations that can be used for the ontologies references (to
     * define links between instances and concepts)
     * @return array list of relations possibles
     */
    public function getEntitiesPossibleRelationsToOthersConcepts() {
        //SILEX:todo
        //create a service and ask to the service the possibles links instead of
        //having a fixed list
        //\SILEX:todo
        return [
            \config::path()['rExactMatch'] => 'skos:exactMatch',
            \config::path()['rCloseMatch'] => 'skos:closeMatch',
            \config::path()['rNarrower'] => 'skos:narrower',
            \config::path()['rBroader'] => 'skos:broader'
        ];
    }
    
    /**
     * Get all the instance definition uri and label
     * @return array the list of the instance definition uri and label existing in the database
     * @example returned array : 
     * [
     *      ["http://www.opensilex.fr/opensilex/traits/id/t001"] => "Trait label",
     *      ...
     * ]
     */
    public function getInstancesDefinitionsUrisAndLabel($sessionToken) {
        $instanceDefinitions = $this->find($sessionToken, $this->attributesToArray());
        $instanceDefinitionsToReturn = [];
        
        if ($instanceDefinitions !== null) {
            //1. get the traits
            foreach($instanceDefinitions as $instanceDefinition) {
                $instanceDefinitionsToReturn[$instanceDefinition->uri] = $instanceDefinition->label;
            }
            
            //2. if there are other pages, get the other traits
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextInstanceDefinitions = $this->getInstancesDefinitionsUrisAndLabel($sessionToken);
                
                $instanceDefinitionsToReturn = array_merge($instanceDefinitionsToReturn, $nextInstanceDefinitions);
            }
            
            return $instanceDefinitionsToReturn;
        }
    }
}
