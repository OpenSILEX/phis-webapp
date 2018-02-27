<?php

//**********************************************************************************************
//                                       _form.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2017
// Subject: creation or update document form
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

require_once '../config/config.php';

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="document-form">
    <?php $form = ActiveForm::begin(); ?>
    

        
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>
    
    <?= $form->field($model, 'creator')->textInput(['maxlength' => true]); ?>
    
    <!-- Faire une dropdown list ?????-->
    <?= $form->field($model, 'language')->textInput(['maxlength' => true, 'placeholder' => 'fr']); ?>
    
    <?= $form->field($model, 'creationDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => ['placeholder' => 'Enter date creation document'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?php 
//    if ($this->params['actualConcernedItem'] == null) {
        if ($model->isNewRecord) {
            echo $form->field($model, 'concernedProjects')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listProjects'],
                'options' => [
                    'placeholder' => 'Select project(s) ...',
                    'value' => $this->params['actualConcernedItem'],
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); 
            
            echo $form->field($model, 'concernedExperiments')->widget(\kartik\select2\Select2::classname(),[
                       'data' => $this->params['listExperiments'],
                       'options' => [
                           'placeholder' => 'Select Experiment(s) ...',
                           'value' => $this->params['actualConcernedItem'],
                           'multiple' => true
                       ],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                   ]); 
        } else {
            echo $form->field($model, 'concernedProjects')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listProjects'],
                'options' => [
                    'placeholder' => 'Select project(s) ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); 
            
            echo $form->field($model, 'concernedExperiments')->widget(\kartik\select2\Select2::classname(),[
                       'data' => $this->params['listExperiments'],
                       'options' => [
                           'placeholder' => 'Select Experiment(s) ...',
                           'multiple' => true
                       ],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                   ]); 
        }
    ?>
    
    <?= $form->field($model, 'documentType')->widget(\kartik\select2\Select2::classname(), [
                        'data' =>$this->params['listDocumentsTypes'],
                        'options' => ['placeholder' => 'Select document type',
                                      'multiple' => false]
                    ]);
    ?>
    
    <?php
    
    if ($model->isNewRecord === true) {
        echo $form->field($model, 'file')->widget(FileInput::classname(), [
            'name' => 'attachment_48[]',
            'options'=>[
                'multiple'=>true,
            ],
            'pluginOptions' => [               
                'maxFileCount' => 1,
                'maxFileSize'=>2000
            ]
            ]);
    } ?>
    
    <?= $form->field($model, 'comment')->textarea(['rows' => '5']) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii' , 'Create') : Yii::t('yii' , 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
    
</div>
