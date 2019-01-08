<?php

//******************************************************************************
//                                       index.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 20 September, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\DataProviderInterface */
/* @var $searchModel app\models\YiiInfrastructureModel */

$this->title = Yii::t('app', '{n, plural, =1{Scientific frame} other{Scientific frames}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructure-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'label',
            'rdfTypeLabel:text:Type',
            [
              'attribute' => 'uri',
              'format' => 'raw',
               'value' => 'uri',
              'filter' =>false,
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['infrastructure/view', 'id' => $model->uri]);
                    },
                ]
            ],
        ],
    ]);
    ?>
</div>
