<?php

//**********************************************************************************************
//                                       _form_dataset.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 4 2017
// Subject: creation of documents via dataset form
//***********************************************************************************************

use yii\widgets\ActiveForm;
use kartik\file\FileInput;

require_once '../config/config.php';

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="document-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
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
    
    <?= $form->field($model, 'documentType')->dropDownList($this->params['listDocumentsTypes'], ['prompt' => '']); ?>
    
    <?= $form->field($model, 'file')->widget(FileInput::classname(), [
            'name' => 'attachment_48[]',
            'options'=>[
                'multiple'=>true,
            ],
            'pluginOptions' => [               
                'maxFileCount' => 1,
                'maxFileSize'=>2000
            ]
            ]);
     ?>
    
    <?= $form->field($model, 'comment')->textarea(['rows' => '5']) ?>
    
    <?php ActiveForm::end(); ?>
    
</div>
