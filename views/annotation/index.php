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
use app\controllers\AnnotationController;
use kartik\select2\Select2;
use app\components\helpers\Vocabulary;
use yii\bootstrap\BaseHtml;
use yii\web\JsExpression;

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
            [
                'label' => YiiAnnotationModel::COMMENTS_LABEL,
                'attribute' => YiiAnnotationModel::COMMENTS,
                'format' => 'html',
                'value' => function ($model) {
                    return implode(('<br>,'), $model->comments);
                }
            ],
            YiiAnnotationModel::CREATOR =>
                [
                'attribute' => YiiAnnotationModel::CREATOR,
                'value' => function($model) {
                    return Vocabulary::prettyUri($model->creator);
                },
                'filter' => Select2::widget([
                    'attribute' => YiiAnnotationModel::CREATOR,
                    'model' => $searchModel,
                    'name' => 'users_instances_filter',
                    'data' => $userInstances,
                    'options' => ['multiple' => false, 'placeholder' => 'Creator of the annotation'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])   
                ],
            YiiAnnotationModel::MOTIVATED_BY => [
                'attribute' => YiiAnnotationModel::MOTIVATED_BY,
                'value' => function($model) {
                    return Vocabulary::prettyUri($model->motivatedBy);
                },
                'filter' => Select2::widget([
                    'attribute' => YiiAnnotationModel::MOTIVATED_BY,
                    'model' => $searchModel,
                    'name' => 'motivation_instances_filter',
                    'data' => ${AnnotationController::MOTIVATION_INSTANCES},
                    'options' => ['multiple' => false, 'placeholder' => 'Motivation of the annotation'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ],
            [
                'label' => YiiAnnotationModel::CREATION_DATE_LABEL,
                'attribute' => YiiAnnotationModel::CREATION_DATE
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a(BaseHtml::icon('eye-open'), ['annotation/view', 'id' => $model->uri]);
                    },
                ]
            ],
        ],
    ]);
    ?>
</div>