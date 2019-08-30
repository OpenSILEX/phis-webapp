<?php
//******************************************************************************
//                               EventAction.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 06 March 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;
use app\models\yiiModels\YiiEventModel;
use app\models\wsModels\WSConstants;

/**
 * Model regrouping the common attributes of both creation and update event models.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventAction extends YiiEventModel {
    
    const EVENT_UNUPDATABLE_DUE_TO_UNUPDATABLE_PROPRTY_LABEL = 
            'The event cannot be updated because one of its specific property'
            . ' isn\'t manageable by the interface';
    
    /**
     * Date timezone offset.
     * @example +01:00
     * @var string
     */
    public $dateTimezoneOffset;
    const DATE_TIMEZONE_OFFSET = 'dateTimezoneOffset';
    const DATE_TIMEZONE_OFFSET_LABEL = 'Timezone offset';
    
    /**
     * Date without timezone.
     * @example 1899-12-31T12:00:00
     * @var string
     */
    public $dateWithoutTimezone;
    const DATE_WITHOUT_TIMEZONE = 'dateWithoutTimezone';
    const DATE_WITHOUT_TIMEZONE_LABEL = self::DATE_LABEL;
    
    /**
     * Concerned items URIs.
     * @example http://www.opensilex.org/demo/DMO2011-1
     * @var array of strings
     */
    public $concernedItemsUris;
    const CONCERNED_ITEMS_URIS = 'concernedItemsUris';
    const CONCERNED_ITEMS_URIS_LABEL = 'Concerned items URIs';
    
    /**
     * Specific property hasPest.
     * @var YiiPropertyModel
     */
    public $propertyHasPest;
    const PROPERTY_HAS_PEST = 'propertyHasPest';
    const PROPERTY_HAS_PEST_LABEL = 'hasPest';
    
    /**
     * Specific properties from.
     * @var YiiPropertyModel
     */
    public $propertyFrom;
    const PROPERTY_FROM = 'propertyFrom';
    const PROPERTY_FROM_LABEL = 'from';
    
    /**
     * Specific properties to.
     * @var YiiPropertyModel
     */
    public $propertyTo;
    const PROPERTY_TO = 'propertyTo';
    const PROPERTY_TO_LABEL = 'to';
    
    /**
     * Specific properties associated with.
     * @var YiiPropertyModel
     */
    public $propertyAssociatedWith;
    const PROPERTY_ASSOCIATED_WITH = 'propertyAssociatedWith';
    const PROPERTY_ASOOCIATED_LABEL = 'Associated with';
    
    /**
     * Specific properties type.
     * @var YiiPropertyModel
     */
    public $propertyType;
    const PROPERTY_TYPE = 'propertyType';
    const PROPERTY_TYPE_LABEL = 'Property type';

    /**
     * The return URL after annotation creation.
     * @var string 
     */
    public $returnUrl;
    const RETURN_URL = "returnUrl";
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[
                self::TYPE, 
                self::DATE_WITHOUT_TIMEZONE,
                self::DATE_TIMEZONE_OFFSET,
                self::DATE_WITHOUT_TIMEZONE,
                self::CONCERNED_ITEMS_URIS
            ],  'required'],
            [[
                self::PROPERTY_HAS_PEST, 
                self::PROPERTY_FROM, 
                self::PROPERTY_TO, 
                self::PROPERTY_ASSOCIATED_WITH, 
                self::RETURN_URL,
            ],  'safe']
        ]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
            parent::attributeLabels(),
            [
                self::CONCERNED_ITEMS_URIS => Yii::t('app', self::CONCERNED_ITEMS_URIS_LABEL),
                self::DATE_TIMEZONE_OFFSET => Yii::t('app', self::DATE_TIMEZONE_OFFSET_LABEL),
                self::DATE_WITHOUT_TIMEZONE => Yii::t('app', self::DATE_WITHOUT_TIMEZONE_LABEL),
                self::PROPERTY_HAS_PEST => Yii::t('app', self::PROPERTY_HAS_PEST_LABEL),
                self::PROPERTY_FROM => Yii::t('app', self::PROPERTY_FROM_LABEL),
                self::PROPERTY_TO => Yii::t('app', self::PROPERTY_TO_LABEL),
                self::PROPERTY_ASSOCIATED_WITH => Yii::t('app', self::PROPERTY_ASOOCIATED_LABEL),
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        $propertiesArray = [];
        foreach ($this->properties as $property) {
            if(isset($property)) {
                $propertiesArray[] = $property->attributesToArray();
            }
        }
        return [
            self::TYPE => $this->rdfType,
            self::DATE => $this->dateWithoutTimezone.$this->dateTimezoneOffset,
            self::CONCERNED_ITEMS_URIS => $this->concernedItemsUris,
            self::PROPERTIES => $propertiesArray,
        ];
    }

    /**
     * Gets the event corresponding to the given URI.
     * @param type $sessionToken
     * @param type $uri
     * @return $this
     */
    public function getEvent($sessionToken, $uri) {
        $event = parent::getEvent($sessionToken, $uri);
        if (!is_string($event)) {
            if (isset($event[WSConstants::TOKEN_INVALID])) {
                return $event;
            } else {
                $this->dateWithoutTimezone = str_replace('T', ' ', substr($event->date, 0, -6));
                $this->dateTimezoneOffset = substr($event->date, -6);
                return $this;
            }
        } else {
            return $event;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
        $this->dateWithoutTimezone = str_replace(" ", "T", $this->dateWithoutTimezone);
        $this->properties = [$this->getPropertyInCreation()];
    }
    
    /**
     * Gets a property object according to the data entered in the creation form.
     * @param type $eventModel
     */
    private function getPropertyInCreation() {
        $property = new YiiPropertyModel();
        switch ($this->rdfType) {
            case Yii::$app->params['moveFrom']:
                $property->value = $this->propertyFrom;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['from'];
                break;
            case Yii::$app->params['moveTo']:
                $property->value = $this->propertyTo;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['to'];
                break;
            case Yii::$app->params['associatedWith']:
                $property->value = $this->propertyAssociatedWith;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['associatedWith'];
                break;
            default : 
                $property = null;
                break;
        }
        return $property;
    }
}
