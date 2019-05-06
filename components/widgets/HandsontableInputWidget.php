<?php
//******************************************************************************
//                    ConcernedItemHandsontableWidget.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;
use yii\helpers\Html;
use himiklab\handsontable\HandsontableWidget;
/**
 * Handsontable grid input widget for Yii2.
 * The input is a hidden input that stores the JSON-encoded string representation of the grid's data.
 *
 * ```php
 * $this->widget('\neam\yii_handsontable_input\widgets\HandsontableInput',[
 *  'id' => 'foo',
 *  'value' => [
 *          ['A1', 'B1', 'C1'],
 *          ['A2', 'B2', 'C2'],
 *  ],
 *  'settings' => [
 *    'colHeaders' => true,
 *    'rowHeaders' => true,
 *  ]
 * ]);
 * ```
 */
class HandsontableInputWidget extends HandsontableWidget
{
    const INPUT_GROUP_DIV_ID = "handsontable-inputs-group";
    const ACTION_BUTTONS_GROUP_DIV = "handsontable-action-buttons-group";
    const ADD_ROW_BUTTON_ID = "handsontable-add-row-button";
    const DELETE_ROW_BUTTON_ID = "handsontable-delete-row-button";
    
    public $inputName;
    
    public function run()
    {     
        echo $this->renderActionButtons() . " " . $this->renderInput();
        
        parent::run();        
        
        $this->view->registerJs("
            
        var form = document.querySelector('form');
        var inputName = '{$this->inputName}';
        form.onsubmit = function() {
            var inputsGroup  = document.querySelector('#" . self::INPUT_GROUP_DIV_ID . "');
            inputsGroup.innerHTML = '';
            var tds = document.querySelectorAll('.htCore td');
            tds.forEach(function(td) {
                var input = document.createElement('input');  
                input.setAttribute('name', inputName);
                input.setAttribute('value', td.innerHTML);
                inputsGroup.appendChild(input);
            });
        };
        
        var addRowButton = document.getElementById('" . self::ADD_ROW_BUTTON_ID . "');
        var deleteRowButton = document.getElementById('" . self::DELETE_ROW_BUTTON_ID . "'); 

        Handsontable.dom.addEvent(addRowButton, 'click', function () {
            {$this->jsVarName}.alter('insert_row', 0);
        });

        Handsontable.dom.addEvent(deleteRowButton, 'click', function () {
            var rowCount = {$this->jsVarName}.countRows();
            {$this->jsVarName}.alter('remove_row', rowCount - 1);
        });
        
        ", \yii\web\View::POS_READY);
    }
    
    protected function renderInput () {
        return "<div id=\"" . self::INPUT_GROUP_DIV_ID . "\" style=\"display:none\"></div>";
    }
    
    protected function renderActionButtons () {
        return  
            "<div id=\"" . self::ACTION_BUTTONS_GROUP_DIV . "\">"
                . Html::buttonInput("Add row", [
                    'id' => self::ADD_ROW_BUTTON_ID,
                    'class' => "btn btn-primary"
                ])
                . Html::buttonInput("Remove last row", [
                    'id' => self::DELETE_ROW_BUTTON_ID,
                    'class' => "btn btn-danger"
                ])
            . "</div>";
    }
}