<?php
//******************************************************************************
//                    ConcernedItemGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 23 Aug, 2018
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use Yii;
use yii\helpers\Html;

/**
 * Used to generate a concerned item GridView with controls.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class ConcernedItemGridViewWidgetWithActions extends ConcernedItemGridViewWidget {

    protected function getColumns(): array {
        return [
            [
            'label' => Yii::t('app', "Concerned items URIs"),
            'value' => function($model){
                $concernedItemDiv = "<div class=\"form-group " . $model->divSpecificClass . "\">";
                $concernedItemDiv .= Html::textInput(
                    $model->inputName,
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
