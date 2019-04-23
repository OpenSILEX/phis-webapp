<?php

//******************************************************************************
//                                       _form-update.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 26 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YiiVectorModel */
/* @var $form yii\widgets\ActiveForm */
/* @var $types array */
/* @var $users  array */
?>
<div class="sensor-form well">
    <?php $form = ActiveForm::begin(); ?>
    
        <?= $form->field($model, 'uri')->textInput([
                    'readonly' => true, 
                    'style' => 'background-color:#C4DAE7;',
                ]); ?>

        <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rdfType')->widget(\kartik\select2\Select2::classname(),[
                    'data' => $types,
                    'options' => [
                        'placeholder' => Yii::t('app', 'Select type ...'),
                        'multiple' => false
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

        <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'serialNumber')->textInput(['maxlength' => true]) ?>
    
        <?= $form->field($model, 'dateOfPurchase')->widget(\kartik\date\DatePicker::className(), [
            'options' => [
                'placeholder' => 'yyyy-mm-dd'],            
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]) ?>
    
        <?= $form->field($model, 'inServiceDate')->widget(\kartik\date\DatePicker::className(), [
            'options' => [
                'placeholder' => 'yyyy-mm-dd'],            
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]) ?>
    
        <?= $form->field($model, 'dateOfLastCalibration')->widget(\kartik\date\DatePicker::className(), [
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
