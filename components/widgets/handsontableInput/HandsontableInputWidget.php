<?php
//******************************************************************************
//                    ConcernedItemHandsontableWidget.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\handsontableInput;

use himiklab\handsontable\HandsontableWidget;
use app\components\widgets\handsontableInput\HandsontableInputWidgetAsset;
use yii\web\View;

/**
 * Handsontable grid input widget for Yii2.
 */
class HandsontableInputWidget extends HandsontableWidget
{
    const INPUT_GROUP_DIV_ID = "handsontable-inputs-group";
    const ACTION_BUTTONS_GROUP_DIV_CLASS = "handsontable-action-buttons-group";
    const ADD_ROW_BUTTON_ID = "handsontable-add-row-button";
    const REMOVE_ROW_BUTTON_ID = "handsontable-delete-row-button";
    
    public $inputName;
    public $title;
    
    public function run()
    {        
        ob_start();
        parent::run();
        ob_end_clean();

        $htmlRendered = $this->render('_handsontable-input', [
            'addRowButtonId' => self::ADD_ROW_BUTTON_ID,
            'removeRowButtonId' => self::REMOVE_ROW_BUTTON_ID,
            'inputGroupDivId' => self::INPUT_GROUP_DIV_ID,
            'actionButtonsGroupDivClass' => self::ACTION_BUTTONS_GROUP_DIV_CLASS,
            'id' => $this->getId()
        ]);
        
        $this->getView()->registerJs(""
                . "var inputName = '{$this->inputName}';"
                . "var inputGroupDivId = '" . self::INPUT_GROUP_DIV_ID . "';"
                . "var addRowButtonId = '" . self::ADD_ROW_BUTTON_ID . "';"
                . "var removeRowButtonId = '" . self::REMOVE_ROW_BUTTON_ID . "';"
                . "var handsonTable;"
                . "", View::POS_HEAD);
        
        $this->getView()->registerJs(""
                . "handsonTable = {$this->jsVarName};"
                . "", View::POS_READY);
        
        HandsontableInputWidgetAsset::register($this->getView());
        
        return $htmlRendered;
    }
    
    protected function renderInput () {
        return "<div id=\"" . self::INPUT_GROUP_DIV_ID . "\" style=\"display:none\"></div>";
    }
    
    protected function renderActionButtons () {
        return  
            "<div class=\"" . self::ACTION_BUTTONS_GROUP_DIV_CLASS . "\">"
                . Html::buttonInput("Add row", [
                    'id' => self::ADD_ROW_BUTTON_ID,
                    'class' => "btn btn-primary"
                ])
                . Html::buttonInput("Remove last row", [
                    'id' => self::REMOVE_ROW_BUTTON_ID,
                    'class' => "btn btn-danger"
                ])
            . "</div>";
    }
}