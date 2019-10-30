<?php
//******************************************************************************
//                       PropertyWidgetWithActions.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use app\components\widgets\PropertyWidget;
use Yii;
use app\models\yiiModels\YiiConcernedItemModel;
use yii\grid\GridView;
use app\components\helpers\Vocabulary;

/**
 * Widget to generate a list of property to modify/create.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class PropertyWidgetWithActions extends PropertyWidget {
    protected function renderValue($value): string {
        
    }

    protected function renderValues($values): string {
        
    }

}
