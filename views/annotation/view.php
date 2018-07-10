<?php

//******************************************************************************
//                                       view.php
//
// Author(s): Arnaud Charleroy <arnaud.charleroy@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 july 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 july 2018
// Subject: implements the view page for a annotation
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\yiiModels\YiiAnnotationModel;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Annotation} other{Annotations}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="annotation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            YiiAnnotationModel::URI,
            YiiAnnotationModel::CREATOR,
            YiiAnnotationModel::MOTIVATED_BY,
            YiiAnnotationModel::CREATION_DATE,
            YiiAnnotationModel::COMMENTS => [
                'attribute' => YiiAnnotationModel::COMMENTS,
                'format' => 'html',
                'value' => function ($model) {
                    return implode(('<br>,'), $model->comments);
                }
            ],
            YiiAnnotationModel::TARGETS => [
                'attribute' => YiiAnnotationModel::TARGETS,
                'format' => 'html',
                'value' => function ($model) {
                    return implode(('<br>,'), $model->targets);
                }
            ],
        ]
    ]);
    ?>

</div>
