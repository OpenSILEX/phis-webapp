<?php
//******************************************************************************
//                             YiiEventModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 02 Jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSEventModel;
use app\models\wsModels\WSConstants;

/**
 * The yii model for an event 
 * @update [Andréas Garcia] 15 Feb., 2019: add properties handling
 * @see app\models\wsModels\WSEventModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class YiiEventModel extends WSActiveRecord {
    
    /**
     * @example http://www.phenome-fppn.fr/id/event/96e72788-6bdc-4f8e-abd1-ce9329371e8e
     * @var string
     */
    public $uri;
    const URI = "uri";
    
    /**
     * @example http://www.opensilex.org/vocabulary/oeev#MoveFrom
     * @var string
     */
    public $rdfType;
    const TYPE = "rdfType";
    
    /**
     * @example 2019-01-02T00:00:00+01:00
     * @var string 
     */
    public $date;
    const DATE = "date";
    
    /**
     * Concerned items of the event
     * @var array
     */
    public $concernedItems; 
    const CONCERNED_ITEMS = "concernedItems";
    
    /**
     * Properties of the event
     * @var array 
     */
    public $properties;
    const PROPERTIES = "properties";
    
    /**
     * Annotations of the event
     * @var array
     */
    public $annotations; 
    const ANNOTATIONS = "annotations";
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSEventModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize 
                : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @return array the rules of the attributes
     */
    public function rules() {
       return [ 
           [[self::URI], 'required'],
           [[
                self::TYPE, 
                self::DATE,
                self::CONCERNED_ITEMS, 
                self::PROPERTIES, 
                self::ANNOTATIONS
            ] , 'safe']
        ]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            self::URI => 'URI', 
            self::TYPE => Yii::t('app', 'Type'), 
            self::DATE => Yii::t('app', 'Date')
        ];
    }
    
    /**
     * Allows to fill the attributes with the information in the array given 
     * @param array $array array key => value which contains the metadata of 
     * an event
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[self::URI];
        $this->rdfType = $array[self::TYPE];
        if ($array[self::CONCERNED_ITEMS]) {
            foreach ($array[self::CONCERNED_ITEMS] as $concernedItemInArray) {
                $eventConcernedItem  = new YiiConcernedItemModel();
                $eventConcernedItem->uri = $concernedItemInArray->uri;
                $eventConcernedItem->rdfType = $concernedItemInArray->typeURI;
                $eventConcernedItem->labels = $concernedItemInArray->labels;
                $this->concernedItems[] = $eventConcernedItem;
            } 
        } 
        $this->properties = [];
        if ($array[self::PROPERTIES]) {
            foreach ($array[self::PROPERTIES] as $propertyInArray) {
                $property  = new YiiPropertyModel();
                $property->arrayToAttributes($propertyInArray);
                $this->properties[] = $property;
            } 
        } 
        $this->annotations = $array[self::ANNOTATIONS];
        $this->date = $array[self::DATE];
    }

    /**
     * Get the detailed event corresponding to the given URI
     * @param type $sessionToken
     * @param type $uri
     * @return $this
     */
    public function getEventDetailed($sessionToken, $uri) {
        $eventDetailed = $this->wsModel->getEventDetailed($sessionToken, $uri);
        if (!is_string($eventDetailed)) {
            if (isset($eventDetailed[WSConstants::TOKEN])) {
                return $eventDetailed;
            } else {
                $this->uri = $uri;
                $this->arrayToAttributes($eventDetailed);
                return $this;
            }
        } else {
            return $eventDetailed;
        }
    }

    /**
     * Call web service and return the list of events types
     * @param sessionToken
     * @return list of the events types
     */
    public function getEventsTypes($sessionToken) {
        $eventConceptUri = "http://www.opensilex.org/vocabulary/oeev#Event";
        $params = [];
        if ($this->pageSize !== null) {
           $params[WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[WSConstants::PAGE] = $this->page;
        }
        
        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getDescendants($sessionToken, $eventConceptUri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[WSConstants::TOKEN])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
}
