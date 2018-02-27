<?php

//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 29 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 29 2017
// Subject: implements the view page for a variable
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiVariableModel */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Variable} other{Variables}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="variable-view">
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <h2><?= Yii::t('app', 'Variable') ?></h2>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'label',
                    'uri',
                    [
                        'attribute' => 'ontologiesReferences',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $toReturn = "";
                            if ($model->ontologiesReferences !== null) {
                                $relations = $model->getEntitiesPossibleRelationsToOthersConcepts();
                                foreach ($model->ontologiesReferences as $ontologyReference) {
                                    $toReturn .= $relations[$ontologyReference->property] 
                                               . " " 
                                               . Html::a($ontologyReference->object, $ontologyReference->seeAlso) . "<br/>";
                                }
                            }
                            return $toReturn;
                        }
                    ],
                    'comment',   
                ],
            ]) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <h2><?= Yii::t('app', 'Trait') ?></h2>
            <?= DetailView::widget([
                'model' => $model->trait,
                'attributes' => [
                    'label',
                    'uri',
                    [
                        'attribute' => 'ontologiesReferences',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $toReturn = "";
                            if ($model->ontologiesReferences !== null) {
                                $relations = $model->getEntitiesPossibleRelationsToOthersConcepts();
                                foreach ($model->ontologiesReferences as $ontologyReference) {
                                    $toReturn .= $relations[$ontologyReference->property] 
                                               . " " 
                                               . Html::a($ontologyReference->object, $ontologyReference->seeAlso) . "<br/>";
                                }
                            }
                            return $toReturn;
                        }
                    ],
                    'comment',   
                ],
            ]) ?>
        </div>
        <div class="col-md-3 col-md-offset-1">
            <h2><?= Yii::t('app', 'Method') ?></h2>
            <?= DetailView::widget([
                'model' => $model->method,
                'attributes' => [
                    'label',
                    'uri',
                    [
                        'attribute' => 'ontologiesReferences',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $toReturn = "";
                            if ($model->ontologiesReferences !== null) {
                                $relations = $model->getEntitiesPossibleRelationsToOthersConcepts();
                                foreach ($model->ontologiesReferences as $ontologyReference) {
                                    $toReturn .= $relations[$ontologyReference->property] 
                                               . " " 
                                               . Html::a($ontologyReference->object, $ontologyReference->seeAlso) . "<br/>";
                                }
                            }
                            return $toReturn;
                        }
                    ],
                    'comment',   
                ],
            ]) ?>
        </div>
        <div class="col-md-3 col-md-offset-1">
            <h2><?= Yii::t('app', 'Unit') ?></h2>
            <?= DetailView::widget([
                'model' => $model->unit,
                'attributes' => [
                    'label',
                    'uri',
                    [
                        'attribute' => 'ontologiesReferences',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $toReturn = "";
                            if ($model->ontologiesReferences !== null) {
                                $relations = $model->getEntitiesPossibleRelationsToOthersConcepts();
                                foreach ($model->ontologiesReferences as $ontologyReference) {
                                    $toReturn .= $relations[$ontologyReference->property] 
                                               . " " 
                                               . Html::a($ontologyReference->object, $ontologyReference->seeAlso) . "<br/>";
                                }
                            }
                            return $toReturn;
                        }
                    ],
                    'comment',   
                ],
            ]) ?>
        </div>
    </div>
</div>