<?php
//******************************************************************************
//                           view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 6 Apr, 2017
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\yiiModels\YiiAnnotationModel;
use app\components\helpers\Vocabulary;

/* @var $this yii\web\View */
/* @var $model app\models\yiiModels\YiiAnnotationModel */
/* Implements the view page for an annotation */
/* @update [Arnaud Charleroy] 23 august, 2018 (add annotation functionality) */

$this->title = YiiAnnotationModel::LABEL;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{' . YiiAnnotationModel::LABEL . '} other{' . YiiAnnotationModel::LABEL . 's}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="annotation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => YiiAnnotationModel::URI,
                'value' => function($model) {
                    return Vocabulary::prettyUri($model->uri);
                }
            ],
            [
                'attribute' => YiiAnnotationModel::CREATOR,
                'value' => function($model) {
                    return Vocabulary::prettyUri($model->creator);
                }
            ],
            [
                'attribute' => YiiAnnotationModel::MOTIVATED_BY,
                'value' => function($model) {
                    return Vocabulary::prettyUri($model->motivatedBy);
                }
            ],
            YiiAnnotationModel::CREATION_DATE,
            YiiAnnotationModel::BODY_VALUES => [
                'attribute' => YiiAnnotationModel::BODY_VALUES,
                'value' => function ($model) {
                     if($model->bodyValues != null){
                         return implode((', '), $model->bodyValues);
                     }
                     return "";
                }
            ],
            YiiAnnotationModel::TARGETS => [
                'attribute' => YiiAnnotationModel::TARGETS,
                'format' => 'html',
                'value' => function ($model) {
                    // Format targets uri
                    $targets = array_map("app\components\helpers\Vocabulary::prettyUri", $model->targets);
                    return implode((', '), $targets);
                }
            ],
        ]
    ]);
    ?>

</div>
