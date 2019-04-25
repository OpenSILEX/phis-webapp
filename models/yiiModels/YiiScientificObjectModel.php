<?php

//**********************************************************************************************
//                                       YiiScientificObjectModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: The Yii model for the scientific objects. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSScientificObjectModel;

/**
 * The yii model for the scientific objects. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSScientificObjectModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiScientificObjectModel extends WSActiveRecord {
    
    /**
     * uri of the scientific object 
     *                      (e.g. http://www.phenome-fppn.fr/pheno3c/o17000001)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * geometry of the scientific objects. 
     *                      (e.g. POLYGON((0 0, 10 0, 10 10, 0 10, 0 0)) )
     * @see https://fr.wikipedia.org/wiki/Well-known_text
     * @var string
     */
    public $geometry;
    const GEOMETRY = "geometry";
    /**
     * the rdf type of the scientific object. Must be in the ontology. 
     *                      (e.g http://www.opensilex.org/vocabulary/oeso#Plot)
     * @var string
     */
    public $type;
    const RDF_TYPE = "rdfType";
    /**
     * the uri of the experiment in which the scientific object is
     *                      (e.g http://www.phenome-fppn.fr/diaphen/DIA2017-1)
     * @var experiment 
     */
    public $experiment;
    const EXPERIMENT = "experiment";
    /**
     * the year of the scientific object 
     *                      (e.g 2017)
     * @var string
     */
    public $year;
    /**
     * the file for the scientific objects creation by csv file
     * @var file
     */
    public $file;
    /** 
     * the alias of the plot 
     *                      (e.g 2/DZ_PG_30/ZM4361/WW/1/DIA2017-05-19)
     * @var string
     */
    public $label;
    const LABEL = "label";
    
    public $species;
    const SPECIES = "species";
    
    public $variety;
    const VARIETY = "variety";
    
    public $modality;
    const MODALITY = "modality";
    
    public $replication;
    const REPLICATION = "replication";
    
    public $parent;
    const ISPARTOF = "ispartof";

    /**
     * Initialize wsModel. In this class, wsModel is a WSScientificObjectModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSScientificObjectModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }
       
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     an scientific object
     * @throws Exception
     */
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
    }

    /**
     * Create an array representing the scientific object
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiScientificObjectModel::URI] = $this->uri;
        $elementForWebService[YiiScientificObjectModel::EXPERIMENT] = $this->experiment;
        $elementForWebService[YiiScientificObjectModel::LABEL] = $this->label;
        $elementForWebService[YiiScientificObjectModel::RDF_TYPE] = $this->type;
        $elementForWebService[YiiScientificObjectModel::GEOMETRY] = $this->geometry;
        $elementForWebService[YiiScientificObjectModel::SPECIES] = $this->species;
        $elementForWebService[YiiScientificObjectModel::VARIETY] = $this->variety;
        $elementForWebService[YiiScientificObjectModel::MODALITY] = $this->modality;
        $elementForWebService[YiiScientificObjectModel::REPLICATION] = $this->replication;
        $elementForWebService[YiiScientificObjectModel::ISPARTOF] = $this->parent;
        
        return $elementForWebService;
    }
    
    /**
     * calls web service and return the list of object types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getObjectTypes($sessionToken) {
        $scientificObjectConceptUri = "http://www.opensilex.org/vocabulary/oeso#ScientificObject";
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $scientificObjectConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * calls web service and return the list of object types of the ontology
     * @see app\models\wsModels\WSUriModel::getDescendants($sessionToken, $uri, $params)
     * @return list of the sensors types
     */
    public function getExperiments($sessionToken) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $scientificObjectConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * Create an array representing the scientific object
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArrayForPut() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiScientificObjectModel::LABEL] = $this->label;
        $elementForWebService[YiiScientificObjectModel::RDF_TYPE] = $this->type;
        $elementForWebService[YiiScientificObjectModel::GEOMETRY] = $this->geometry;
        $elementForWebService[YiiScientificObjectModel::ISPARTOF] = $this->parent;
        
        $properties = [];
        
        if (!empty($this->species)) {
            $species["rdfType"] = Yii::$app->params['Species'];
            $species["relation"] = Yii::$app->params['hasSpecies'];
            $species["value"] = $this->species;
            $properties[] = $species;
        }
        
        if (!empty($this->variety)) {
            $variety["rdfType"] = Yii::$app->params['Variety'];
            $variety["relation"] = Yii::$app->params['hasVariety'];
            $variety["value"] = str_replace(" ", "_", $this->variety);
            $properties[] = $variety;
        }
        
        if (!empty($this->modality)) {
            $modality["relation"] = Yii::$app->params['hasExperimentModalities'];
            $modality["value"] = $this->modality;
            $properties[] = $modality;
        }
        
        if (!empty($this->replication)) {
            $replication["relation"] = Yii::$app->params['hasReplication'];
            $replication["value"] = $this->replication;
            $properties[] = $replication;
        }
        
        $elementForWebService["properties"] = $properties;
        
        return $elementForWebService;
    }
    
    /**
     * while the species service is not implemented, get a fixed species uris 
     * list
     * @return list of the species uri.
     */
    public function getSpeciesUriList() {
        return [
            "http://www.phenome-fppn.fr/id/species/betavulgaris", 
            "http://www.phenome-fppn.fr/id/species/brassicanapus",
            "http://www.phenome-fppn.fr/id/species/canabissativa",
            "http://www.phenome-fppn.fr/id/species/glycinemax",
            "http://www.phenome-fppn.fr/id/species/gossypiumhirsutum",
            "http://www.phenome-fppn.fr/id/species/helianthusannuus",
            "http://www.phenome-fppn.fr/id/species/linumusitatissum",
            "http://www.phenome-fppn.fr/id/species/lupinusalbus",
            "http://www.phenome-fppn.fr/id/species/ordeumvulgare",
            "http://www.phenome-fppn.fr/id/species/orizasativa",
            "http://www.phenome-fppn.fr/id/species/pennisetumglaucum",
            "http://www.phenome-fppn.fr/id/species/pisumsativum",
            "http://www.phenome-fppn.fr/id/species/populus",
            "http://www.phenome-fppn.fr/id/species/sorghumbicolor",
            "http://www.phenome-fppn.fr/id/species/teosinte",
            "http://www.phenome-fppn.fr/id/species/triticumaestivum",
            "http://www.phenome-fppn.fr/id/species/triticumturgidum",
            "http://www.phenome-fppn.fr/id/species/viciafaba",
            "http://www.phenome-fppn.fr/id/species/zeamays",
            "http://www.phenome-fppn.fr/id/species/maize"
        ];
    }
    
    /**
     * Update the metadata of a given scientific object in the context of a given experiment.
     * @see \app\models\wsModels\WSScientificObjectModel::putByExperiment($sessionToken, $uri, $experiment, $params)
     * @param string $sessionToken
     * @param string $uri
     * @param string $experiment
     * @return mixed the update result.
     */
    public function updateByExperiment($sessionToken, $uri, $experiment) {
        $params = $this->attributesToArrayForPut();
        $requestRes = $this->wsModel->putByExperiment($sessionToken, $uri, $experiment, $params);
        return $requestRes;
    }
}
