<?php

//**********************************************************************************************
//                                       _search.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  March, 2017
// Subject: search form for projects
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'uri') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'acronyme') ?>

    <?= $form->field($model, 'dateStart') ?>

    <?= $form->field($model, 'dateEnd') ?>

    <?php // echo $form->field($model, 'financialSupport') ?>

    <?php // echo $form->field($model, 'financialName') ?>

    <?php // echo $form->field($model, 'keywords') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'objective') ?>

    <?php // echo $form->field($model, 'parentProject') ?>
    
    <?php // echo $form->field($model, 'scientificContact') ?>
    
    <?php // echo $form->field($model, 'administrativeContact') ?>
    
    <?php // echo $form->field($model, 'projectCoordinator') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('yii', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>