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

/**
 * Event POST model
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventCreation extends EventAction {
    
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
     * @inheritdoc
     */
    public function rules() {
        return array_merge(
            parent::rules(), [
            [[
                self::CREATOR,
            ],  'required'],
            [[
                self::DESCRIPTION
            ],  'safe']
        ]);
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
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        return array_merge(
            parent::attributesToArray(),[
            self::DESCRIPTION => $this->description,
            self::CREATOR => $this->creator
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);

        foreach ($this->concernedItemsUris as $concernedItemUri) {
            $concernedItem = new YiiConcernedItemModel();
            $concernedItem->uri = $concernedItemUri;
            $this->concernedItems[] = $concernedItem;
        }
    }
}
