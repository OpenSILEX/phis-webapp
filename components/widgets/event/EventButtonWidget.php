<?php
//******************************************************************************
//                         EventButtonWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 05 March. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\event;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\icons\Icon;

/**
 * A widget used to generate an event button
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventButtonWidget extends Widget {

    CONST ADD_EVENT_LABEL = 'Add event';
    CONST CONCERNED_ITEMS_NOT_SET_LABEL = 'The concerned items are not set';
    CONST CONCERNED_ITEM_LIST_NOT_A_ARRAY = 'The concerned items list is not an array';
    CONST CONCERNED_ITEM_LIST_EMPTY = 'The concerned items list is empty';
        
    /**
     * Define if button is displayed as a button (false) or as a link (true)
     * @var boolean
     */    
    public $asLink = false;
    const AS_LINK = "asLink";
           
    /**
     * Define the items which will be annoted
     * @var array
     */
    public $concernedItemsUris;
    const CONCERNED_ITEMS_URIS = "concernedItemsUris";

    /**
     * Render the event button
     * @return string the string rendered
     */
    public function run() {
        //SILEX:conception
        // Maybe create a widget bar and put buttons in it to use the same style
        //\SILEX:conception
        $uriArray = [
                'event/create',
                'concernedItemsUris' => $this->concernedItemsUris,
                'returnUrl' => Url::current()
            ];
        
        $linkClasses = [];
        if (!$this->asLink) {
            $linkLabel = Icon::show('flag', [], Icon::FA) . " " . Yii::t('app', self::ADD_EVENT_LABEL);
            $linkAttributes = ['class' => 'btn btn-default'];
        } else {
            $linkLabel = '<span class="fa fa-flag"></span>';
        }
        $linkAttributes["title"] = Yii::t('app', self::ADD_EVENT_LABEL);
                
        return Html::a(
                    $linkLabel,
                    $uriArray, 
                    $linkAttributes
                );
    }
}
