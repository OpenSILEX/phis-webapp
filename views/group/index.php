<?php

//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: index of groups (with search)
//***********************************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Yii::$app->session['isAdmin']) { ?>
    <p>
        <?= Html::a(Yii::t('yii', 'Create') . ' '. Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
              'attribute' => 'uri',
              'format' => 'raw',
               'value' => 'uri',
              'filter' =>false,
            ],
            'name',
            [
                'attribute' => 'level',
                'format' => 'raw',
                'filter' => \kartik\select2\Select2::widget([
                            'attribute' => 'level',
                            'model' => $searchModel,
                            'data' => [
                                'Guest' => Yii::t('app', 'Guest'),
                                'Owner' => Yii::t('app', 'Owner')],
                            'options' => [
                                'placeholder' => Yii::t('app', 'Select level'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]),
            ],
            
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['group/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>