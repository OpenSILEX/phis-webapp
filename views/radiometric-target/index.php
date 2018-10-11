<?php

//******************************************************************************
//                                       index.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 01 Oct, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RadiometricTargetSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="radiometric-target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
            if (Yii::$app->session['isAdmin']) {
                echo Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) . "\t";
            }
        ?>
    </p>
    
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'label',
            'uri',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['radiometric-target/view', 'id' => $model->uri]);
                    },
                ]
            ],
        ],
    ]);
    ?>
</div>