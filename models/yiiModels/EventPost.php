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
 * Search action for the events
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
     * @example http://www.phenome-fppn.fr/diaphen/id/agent/marie_dupond"
     * @var string
     */
    public $creator;
    const CREATOR = 'creator';
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [[
            [
                YiiEventModel::TYPE,
                YiiEventModel::DATE,
                self::DESCRIPTION,
                self::CREATOR
            ],  'safe']]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
            parent::attributeLabels(),
            [
                self::DESCRIPTION => Yii::t('app', 'Description'),
                self::CREATOR => Yii::t('app', 'Creator')
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        return [
            YiiEventModel::TYPE => $this->type,
            YiiEventModel::DATE => $this->date,
            self::DESCRIPTION => $this->description,
            self::CREATOR => $this->creator,
            'concernedItemsUris' => [
      "http://www.opensilex.org/andreas-dev/2019/s19001" ,
      "http://www.opensilex.org/andreas-dev/2019/s19002"] 
        ];
    }
}
