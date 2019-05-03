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
use yii\base\Widget;
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
class HandsontableInputWidget extends Widget
{
    const INPUT_GROUP_DIV = "handsontable-input-group";
    
    protected $model;
    protected $jsWidget;
    
    /**
     * @var string $settings
     * @see https://github.com/handsontable/jquery-handsontable/wiki
     */
    public $settings = [];
    public $inputName;
    
    public function init()
    {
        parent::init();
        $this->view->registerJs("
            
        var form = document.querySelector(\"form\");
        var inputName = \"" . $this->inputName . "\";
        form.onsubmit = function() {
            var inputsGroup  = document.querySelector(\"#" . self::INPUT_GROUP_DIV . "\");
            inputsGroup.innerHTML = \"\";
            var tds = document.querySelectorAll(\".htCore td\");
            tds.forEach(function(td) {
                var input = document.createElement(\"input\");  
                input.setAttribute(\"name\", inputName);
                input.setAttribute(\"value\", td.innerHTML);
                inputsGroup.appendChild(input);
            });
        };
        
        ");
    }
    
    public function run()
    {
        $this->jsWidget = HandsontableWidget::begin(['settings' => $this->settings]);
        $this->jsWidget->end();
        return $this->renderInput();
    }
    
    protected function renderInput () {
        return "<div id=\"" . self::INPUT_GROUP_DIV . "\" style=\"display:none\"></div>";
    }
}