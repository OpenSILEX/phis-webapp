<?php

//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 5 2017
// Subject: index of agronomical objects (with search)
//***********************************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AgronomicalObjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Agronomical Object} other{Agronomical Objects}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="agronomicalobject-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('yii', 'Create'), ['create-csv'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('yii', 'Download Search Result'), ['download-csv', 'model' => $searchModel], ['class' => 'btn btn-primary']) ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'uri',
            'alias',
            [
                'attribute' => 'typeAgronomicalObject',
                'format' => 'raw',
                'value' => function($model, $key, $index) {
                    return explode("#", $model->typeAgronomicalObject)[1];
                }
            ],
            [
                'attribute' => 'experiment',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    return Html::a($model->experiment, ['experiment/view', 'id' => $model->experiment]);
                },
                'filter' => \kartik\select2\Select2::widget([
                            'attribute' => 'experiment',
                            'model' => $searchModel,
                            'data' => $this->params['listExperiments'],
                            'options' => [
                                'placeholder' => 'Select experiment alias...'
                            ]
                        ]),
            ]
        ],
    ]); ?>
</div>