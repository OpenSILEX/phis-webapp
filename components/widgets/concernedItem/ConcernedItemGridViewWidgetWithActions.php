<?php
//******************************************************************************
//                    ConcernedItemGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 23 Aug, 2018
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\concernedItem;

use Yii;
use yii\helpers\Html;

/**
 * Used to generate a concerned item GridView with controls.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class ConcernedItemGridViewWidgetWithActions extends ConcernedItemGridViewWidget {
    
    CONST INPUT_MODEL_CLASS = "inputModelClass";
    public $inputModelClass;
    
    CONST INPUT_MODEL_CONCERNED_ITEMS_URIS_ATTRIBUTE_NAME = "inputModelConcernedItemsUrisAttributeName";
    public $inputModelConcernedItemsUrisAttributeName;

    protected function getColumns(): array {
        return [
            [
            'label' => Yii::t('app', "Concerned items URIs"),
            'value' => function($model){
                $concernedItemDiv = "<div class=\"form-group " . "field-" . $this->inputModelClass . "-concerneditem\">";
                $concernedItemDiv .= Html::textInput(
                    $this->inputModelClass . "[" . $this->inputModelConcernedItemsUrisAttributeName . "][]",
                    $model->uri, 
                    [
                        'class' => 'form-control',
                        'readonly'=> true
                    ]);
                $concernedItemDiv .= "</div>";
                return $concernedItemDiv;
            },
            'format' => 'raw'
            ]
        ];
    }
}
