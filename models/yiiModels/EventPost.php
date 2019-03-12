<?php
//******************************************************************************
//                                  EventPost.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 06 March 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;
use app\models\yiiModels\YiiEventModel;

/**
 * Event POST model
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventPost extends YiiEventModel {
    
    /**
     * Description
     * @example The Pest attack lasted 20 minutes
     * @var string
     */
    public $description;
    const DESCRIPTION = 'description';
    
    /**
     * Creator URI
     * @example http://www.phenome-fppn.fr/diaphen/id/agent/marie_dupond
     * @var string
     */
    public $creator;
    const CREATOR = 'creator';
    
    /**
     * Creator timezone offset
     * @example +01:00
     * @var string
     */
    public $creatorTimeZoneOffset;
    const CREATOR_TIMEZONE_OFFSET = 'creatorTimeZoneOffset';
    
    /**
     * Date without timezone
     * @example 1899-12-31T12:00:00
     * @var string
     */
    public $dateWithoutTimezone;
    const DATE_WITHOUT_TIMEZONE = 'dateWithoutTimezone';
    
    /**
     * Concerned items URI
     * @example http://www.opensilex.org/demo/DMO2011-1
     * @var array of strings
     */
    public $concernedItemsUris;
    const CONCERNED_ITEMS_URIS = 'concernedItemsUris';
    
    /**
     * Specific property hasPest
     * @var YiiPropertyModel
     */
    public $propertyHasPest;
    const PROPERTY_HAS_PEST = 'propertyHasPest';
    const PROPERTY_HAS_PEST_LABEL = 'hasPest';
    
    /**
     * Specific properties from
     * @var YiiPropertyModel
     */
    public $propertyFrom;
    const PROPERTY_FROM = 'propertyFrom';
    const PROPERTY_FROM_LABEL = 'from';
    
    /**
     * Specific properties to
     * @var YiiPropertyModel
     */
    public $propertyTo;
    const PROPERTY_TO = 'propertyTo';
    const PROPERTY_TO_LABEL = 'to';
    
    /**
     * Specific properties type
     * @var YiiPropertyModel
     */
    public $propertyType;
    const PROPERTY_TYPE = 'propertyType';
    const PROPERTY_TYPE_LABEL = 'Property type';
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[
                YiiEventModel::TYPE, 
                self::DATE_WITHOUT_TIMEZONE,
                self::CREATOR,
                self::CREATOR_TIMEZONE_OFFSET,
                self::DATE_WITHOUT_TIMEZONE,
                self::CONCERNED_ITEMS_URIS
            ],  'required'],
            [[
                self::DESCRIPTION, 
                self::PROPERTY_HAS_PEST, 
                self::PROPERTY_FROM, 
                self::PROPERTY_TO, 
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
                self::DESCRIPTION => Yii::t('app', 'Description'),
                self::CREATOR => Yii::t('app', 'Creator'),
                self::CONCERNED_ITEMS_URIS => Yii::t('app', 'Concerned items URIs'),
                self::CREATOR_TIMEZONE_OFFSET => Yii::t('app', 'Timezone offset'),
                self::DATE_WITHOUT_TIMEZONE => Yii::t('app', 'Date'),
                self::PROPERTY_HAS_PEST => Yii::t('app', self::PROPERTY_HAS_PEST_LABEL),
                self::PROPERTY_FROM => Yii::t('app', self::PROPERTY_FROM_LABEL),
                self::PROPERTY_TO => Yii::t('app', self::PROPERTY_TO_LABEL),
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
            self::DATE => $this->dateWithoutTimezone.$this->creatorTimeZoneOffset,
            self::DESCRIPTION => $this->description,
            self::CREATOR => $this->creator,
            self::CONCERNED_ITEMS_URIS => $this->concernedItemsUris,
            self::PROPERTIES => $propertiesArray,
        ];
    }
}
