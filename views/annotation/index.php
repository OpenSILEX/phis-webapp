<?php

//******************************************************************************
//                                       index.php
//
// Author(s): Arnaud Charleroy <arnaud.charleroy>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 9 july 2018
// Contact: arnaud.charleroy, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  9 july 2018
// Subject: index of annotations (with search)
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\yiiModels\YiiAnnotationModel;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AnnotationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Annotation} other{Annotations}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="annotation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            YiiAnnotationModel::URI,
            YiiAnnotationModel::CREATOR,
             YiiAnnotationModel::MOTIVATED_BY => [
                'attribute' => YiiAnnotationModel::MOTIVATED_BY,
                'filter' => Html::activeDropDownList($searchModel, YiiAnnotationModel::MOTIVATED_BY, $motivationIndividuals, ['class' => 'form-control', 'prompt' => 'Select Category']),
            ],
            YiiAnnotationModel::CREATION_DATE,
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['annotation/view', 'id' => $model->uri]);
                    },
                ]
            ],
        ],
    ]);
    ?>
</div>