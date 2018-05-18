<?php

//******************************************************************************
//                                       view.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject: implements the view page for a vector
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiVectorModel */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vector-view">

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
            [
              'attribute' => 'personInCharge',
              'format' => 'raw',
              'value' => function ($model) {
                    return Html::a($model->personInCharge, ['user/view', 'id' => $model->personInCharge]);
                },
            ],
        ]
    ]); ?>
 
</div>