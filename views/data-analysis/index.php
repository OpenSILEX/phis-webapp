<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\BaseHtml;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', 
        '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}',
        ['n' => 2]
        );
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'display_name',
        'description',
        [
            'value' => 'application_url',
            'format' => 'raw',
              'value' => function ($model, $key, $index) {
                return Html::a(BaseHtml::icon('eye-open'), ['data-analysis/view', 'url' => $model->application_url]);
              },
        ],
    ],
]); 