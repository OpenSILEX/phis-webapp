<?php

//******************************************************************************
//                                       index.php
//
// Author(s): Andréas Garcia <andreas.garcia@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 02 janvier 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', '{n, plural, =1{Event} other{Events}}'
        , ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
            if (Yii::$app->session['isAdmin']) {
                echo Html::a(
                        Yii::t('yii', 'Create') . ' '. Yii::t('app'
                                , '{n, plural, =1{Sensor} other{Sensors}}'
                                , ['n' => 1])
                        , ['create']
                        , ['class' => 'btn btn-success']
                    ) . "\t";
                echo Html::a(Yii::t('app', 'Characterize Sensor')
                        , ['characterize'], ['class' => 'btn btn-success']);
            }
        ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'uri',
            'label',
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
            'dateOfLastCalibration',
            [
              'attribute' => 'personInCharge',
              'format' => 'raw',
              'value' => function ($model, $key, $index) {
                    return Html::a($model->personInCharge, ['user/view', 'id' => $model->personInCharge]);
                },
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['event/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>