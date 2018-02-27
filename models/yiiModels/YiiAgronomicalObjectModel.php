<?php

//**********************************************************************************************
//                                       YiiAgronomicalObjectModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: The Yii model for the agronomical objects. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSAgronomicalObjectModel;

/**
 * The yii model for the agronomical objects. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSAgronomicalObjectModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiAgronomicalObjectModel extends WSActiveRecord {
    
    /**
     * uri of the agronomical object 
     *                      (e.g. http://www.phenome-fppn.fr/pheno3c/o17000001)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * geometry of the agronomical objects. 
     *                      (e.g. POLYGON((0 0, 10 0, 10 10, 0 10, 0 0)) )
     * @see https://fr.wikipedia.org/wiki/Well-known_text
     * @var string
     */
    public $geometry;
    /**
     * the rdf type of the agronomical object. Must be in the ontology. 
     *                      (e.g http://www.phenome-fppn.fr/vocabulary/2017#Plot)
     * @var string
     */
    public $type;
    /**
     * the uri of the experiment in which the agronomical object is
     *                      (e.g http://www.phenome-fppn.fr/diaphen/DIA2017-1)
     * @var experiment 
     */
    public $experiment;
    const EXPERIMENT = "experiment";
    /**
     * the year of the agronomical object 
     *                      (e.g 2017)
     * @var string
     */
    public $year;
    /**
     * the file for the agronomical objects creation by csv file
     * @var file
     */
    public $file;
    /** 
     * the alias of the plot 
     *                      (e.g 2/DZ_PG_30/ZM4361/WW/1/DIA2017-05-19)
     * @var string
     */
    public $alias;
    const ALIAS = "alias";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSAgronomicalObjectModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSAgronomicalObjectModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['geometry', 'type'], 'required'],
          [['uri', 'geometry', 'type', 'experiment'], 'string'],
          [['file'], 'safe'],
          [['file'], 'file', 'extensions' => 'csv']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
          'uri' => Yii::t('app', 'URI'),
          'geometry' => Yii::t('app', 'Geometry'),
          'type' => Yii::t('app', 'Type'),
          'experiment' => Yii::t('app', 'Experiment'),
          'year' => Yii::t('app', 'Year'),
          'file' => Yii::t('app', 'File')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     an agronommical object
     * @throws Exception
     */
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
    }

    /**
     * Create an array representing the agronomical object
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService[YiiModelsConstants::PAGE] = $this->page;
        $elementForWebService[YiiModelsConstants::PAGE_SIZE] = $this->pageSize;
        $elementForWebService[YiiAgronomicalObjectModel::URI] = $this->uri;
        $elementForWebService[YiiAgronomicalObjectModel::EXPERIMENT] = $this->experiment;
        $elementForWebService[YiiAgronomicalObjectModel::ALIAS] = $this->alias;
        
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
}
