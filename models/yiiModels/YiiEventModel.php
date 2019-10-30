<?php
//******************************************************************************
//                             YiiEventModel.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 2 Jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;

use yii\data\ArrayDataProvider;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUriModel;
use app\models\wsModels\WSEventModel;
use app\models\wsModels\WSConstants;

/**
 * The Yii model for an event 
 * @see app\models\wsModels\WSEventModel
 * @update [Andréas Garcia] 15 Feb., 2019: add properties handling
 * @update [Andréas Garcia] 16 Feb., 2019: use events/{uri}/annotations service
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class YiiEventModel extends WSActiveRecord {
    
    const EVENTS_LABEL = "Events";
    const EVENT_LABEL = "Event";
    
    /**
     * @example http://www.phenome-fppn.fr/id/event/96e72788-6bdc-4f8e-abd1-ce9329371e8e
     * @var string
     */
    public $uri;
    const URI = "uri";
    const URI_LABEL = "URI";
    
    /**
     * @example http://www.opensilex.org/vocabulary/oeev#MoveFrom
     * @var string
     */
    public $rdfType;
    const TYPE = "Type";
    const TYPE_LABEL = "Type";
    
    /**
     * @example 2019-01-02T00:00:00+01:00
     * @var string 
     */
    public $date;
    const DATE = "date";
    const DATE_LABEL = "Date";
    
    /**
     * Concerned items of the event
     * @var array
     */
    public $concernedItems; 
    const CONCERNED_ITEMS = "concernedItems";
    const CONCERNED_ITEMS_LABEL = "Concerned items";
    
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
    const ANNOTATIONS = "Annotations";
    
    
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
            [[
                self::URI, 
                self::DATE,
                self::TYPE, 
                self::CONCERNED_ITEMS
            ], 'required'],
            [[
                self::PROPERTIES
            ] , 'safe']
        ]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            self::URI => Yii::t('app', self::URI_LABEL), 
            self::TYPE => Yii::t('app', self::TYPE_LABEL), 
            self::DATE => Yii::t('app', self::DATE_LABEL), 
            self::CONCERNED_ITEMS => Yii::t('app', self::CONCERNED_ITEMS_LABEL)
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
        $this->date = $array[self::DATE];
    }

    /**
     * Gets the event corresponding to the given URI
     * @param type $sessionToken
     * @param type $uri
     * @return $this
     */
    public function getEvent($sessionToken, $uri) {
        $event = $this->wsModel->getEvent($sessionToken, $uri);
        if (!is_string($event)) {
           if (isset($event->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
                    && $event->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN_INVALID) {
                    return \app\models\wsModels\WSConstants::TOKEN_INVALID;
                    } else {
                $this->arrayToAttributes($event);
                $this->uri = $uri;
                return $this;
            }
        } else {
            return $event;
        }
    }
    
    
    /**
     * Get the event's annotations
     * @param type $sessionToken
     * @param type $searchParams
     * @return the event's annotations provider
     */
    public function getEventAnnotations($sessionToken, $searchParams) {
        $searchParams[YiiEventModel::URI] = $searchParams[WSActiveRecord::ID];
        $response = $this->wsModel->getEventAnnotations($sessionToken, $searchParams);
        if (!is_string($response)) {
            if (isset($response[WSConstants::TOKEN_INVALID])) {
                return $response;
            } else {              
                $annotationWidgetPageSize = Yii::$app->params['annotationWidgetPageSize'];  
                return new ArrayDataProvider([
                    'allModels' => $response,
                    'pagination' => [
                        'pageSize' => $annotationWidgetPageSize,
                    ]
                ]);;
            }
        } else {
            return $response;
        }
    }
    
  

    /**
     * Calls the web service and returns the list of events types
     * @param sessionToken
     * @return list of the events types
     */
    public function getEventsTypes($sessionToken) {
        $eventConceptUri = Yii::$app->params['event'];
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
            if (isset($requestRes[WSConstants::TOKEN_INVALID])) {
                return WSConstants::TOKEN_INVALID;
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
}
