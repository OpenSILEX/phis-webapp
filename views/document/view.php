<?php

//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June, 19 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June 19, 2017
// Subject: implements the view page for a document
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-view">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
    <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Download'), ['download', 'id' => $model->uri, 'format' => $model->format], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uri',
            'title',
            'creator',
            'documentType',   
            'creationDate',
            'language',
            'comment',
            [
              'attribute' => 'concernedExperiments',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->concernedExperiments) > 0) {
                    foreach($model->concernedExperiments as $concernedExperiment) {
                        $toReturn .= Html::a($concernedExperiment, ['experiment/view', 'id' => $concernedExperiment]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            [
              'attribute' => 'concernedProjects',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->concernedProjects) > 0) {
                    foreach($model->concernedProjects as $concernedProjects) {
                        $toReturn .= Html::a($concernedProjects, ['project/view', 'id' => $concernedProjects]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            [
              'attribute' => 'concernedSensors',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->concernedSensors) > 0) {
                    foreach($model->concernedSensors as $concernedSensor) {
                        $toReturn .= Html::a($concernedSensor, ['sensor/view', 'id' => $concernedSensor]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
            [
              'attribute' => 'concernedVectors',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->concernedVectors) > 0) {
                    foreach($model->concernedVectors as $concernedVector) {
                        $toReturn .= Html::a($concernedVector, ['vector/view', 'id' => $concernedVector]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
        ],
    ]) ?>
    
</div>
