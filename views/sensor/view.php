<?php

//******************************************************************************
//                                       view.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 30 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  30 mars 2018
// Subject: implements the view page for a sensor
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sensor-view">

    <h1><?= Html::encode($this->title) ?></h1>

<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'label',
            'uri',
            [
              'attribute' => 'rdfType',
              'format' => 'raw',
              'value' => function ($model) {
                return explode("#", $model->rdfType)[1];
              }
            ],
            'brand',
            'serialNumber',
            'inServiceDate',
            'dateOfPurchase',
            'dateOfLastCalibration',
            [
              'attribute' => 'personInCharge',
              'format' => 'raw',
              'value' => function ($model) {
                    return Html::a($model->personInCharge, ['user/view', 'id' => $model->personInCharge]);
                },
            ],
            [
              'attribute' => 'properties',
              'format' => 'raw',
              'value' => function ($model) {
                    $toReturn = "<ul>";
                    foreach ($model->properties as $property) {
                        $propertyLabel = explode("#", $property->relation)[1];
                        if ($propertyLabel !== "type"
                                && $propertyLabel !== "label"
                                && $propertyLabel !== "inServiceDate"
                                && $propertyLabel !== "personInCharge"
                                && $propertyLabel !== "serialNumber"
                                && $propertyLabel !== "dateOfPurchase"
                                && $propertyLabel !== "dateOfLastCalibration"
                                && $propertyLabel !== "hasBrand"
                                && $propertyLabel !== "hasLens") {
                            $toReturn .= "<li>"
                                    . "<b>" . explode("#", $property->relation)[1] . "</b>"
                                    . " : "
                                    . $property->value
                                    . "</li>";
                        } else if ($propertyLabel === "hasLens") {
                            $toReturn .= "<li>"
                                    . "<b>" . explode("#", $property->relation)[1] . "</b>"
                                    . " : "
                                    . Html::a($property->value, ['view', 'id' => $property->value])
                                    . "</li>";
                        }
                    }
                    $toReturn .= "</ul>";
                    return $toReturn;
                },
            ]
        ]
    ]); ?>
 
</div>
