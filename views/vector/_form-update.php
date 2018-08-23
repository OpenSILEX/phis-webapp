<?php

//******************************************************************************
//                                       _form-update.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 24 mai 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  24 mai 2018
// Subject:
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YiiVectorModel */
/* @var $form yii\widgets\ActiveForm */
/* @var $types array */
/* @var $users  array */
?>

<div class="vector-form well">
    <?php $form = ActiveForm::begin(); ?>
    
        <?= $form->field($model, 'uri')->textInput([
                    'readonly' => true, 
                    'style' => 'background-color:#C4DAE7;',
                ]); ?>

        <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rdfType')->widget(\kartik\select2\Select2::classname(),[
                    'data' => $types,
                    'options' => [
                        'placeholder' => 'Select type ...',
                        'multiple' => false
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

        <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'serialNumber')->textInput(['maxlength' => true]) ?>
 well
        <?= $form->field($model, 'inServiceDate')->widget(\kartik\date\DatePicker::className(), [
            'options' => [
                'placeholder' => 'yyyy-mm-dd'],            
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]) ?>

        <?= $form->field($model, 'dateOfPurchase')->widget(\kartik\date\DatePicker::className(), [
            'options' => [
                'placeholder' => 'yyyy-mm-dd'],            
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]) ?>

        <?= $form->field($model, 'personInCharge')->widget(\kartik\select2\Select2::classname(),[
                    'data' => $users,
                    'options' => [
                        'placeholder' => 'Select person in charge ...'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('yii', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>