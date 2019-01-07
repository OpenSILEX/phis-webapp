<?php

//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  March, 2017
// Subject: index of projects (with search)
//***********************************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('yii', 'Create') . ' '. Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'uri',
            //'name',
            'acronyme',
            'financialSupport',
            [
              'attribute' => 'dateStart',
              'format' => 'raw',
               'value' => 'dateStart',
//              'filter' => DatePicker::widget([
//                    'model' => $searchModel, 
//                    'attribute' => 'dateStart',
//                    'pluginOptions' => [
//                        'autoclose'=>true,
//                        'format' => 'yyyy-mm-dd'
//                    ]
//                ]),
                'filter' =>false,
            ],
            [
              'attribute' => 'dateEnd',
              'format' => 'raw',
               'value' => 'dateEnd',
//              'filter' => DatePicker::widget([
//                    'model' => $searchModel, 
//                    'attribute' => 'dateEnd',
//                    'pluginOptions' => [
//                        'autoclose'=>true,
//                        'format' => 'yyyy-mm-dd'
//                    ]
//                ]),
                'filter' =>false,
            ],
            // 'subprojectType',
            // 'financialName',
            // 'keywords',
            // 'description',
            // 'objective',
            // 'parentProject',
            // 'scientificContact',
            // 'administrativeContact',
            // 'projectCoordinator'

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['project/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>
