<?php
//******************************************************************************
//                         EventButtonWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 05 March, 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;
use kartik\icons\Icon;
use app\models\yiiModels\YiiEventModel;

/**
 * A widget used to generate an event button
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventButtonWidget extends Widget {

    CONST ADD_EVENT_LABEL = 'Add event';
    CONST CONCERNED_ITEMS_NOT_SET_LABEL = 'The concerned items are not set';
    CONST CONCERNED_ITEM_LIST_NOT_A_ARRAY = 'The concerned items list is not an array';
    CONST CONCERNED_ITEM_LIST_EMPTY = 'The concerned items list is empty';
    
    public $concernedItems;
    const CONCERNED_ITEMS = "concernedItems";

    public function init() {
        parent::init();
        // must be not null
        if ($this->concernedItems === null) {
           throw new \Exception(CONCERNED_ITEM_NOT_SET_LABEL);
        }
         // must be an array
        if (!is_array($this->concernedItems)) {
          throw new \Exception(CONCERNED_ITEM_LIST_NOT_A_ARRAY);
        }
         // must contains at least one element
        if (empty($this->concernedItems)) {
            throw new \Exception(CONCERNED_ITEM_LIST_EMPTY);
        }
    }

    /**
     * Render the event button
     * @return string the string rendered
     */
    public function run() {
        //SILEX:conception
        // Maybe create a widget bar and put buttons in it to use the same style
        //\SILEX:conception
        return Html::a(
            Icon::show('flag', [], Icon::FA) . " " . Yii::t('app', self::ADD_EVENT_LABEL),
            [
                'annotation/create',
                YiiEventModel::CONCERNED_ITEMS => $this->concernedItems,
            ], 
            [
                'class' => 'btn btn-default',
            ] 
        );
    }

}
