<?php

//**********************************************************************************************
//                                       _form.php 
// PHIS-SILEX
// Copyright © INRA 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Subject: creation or update document form
//***********************************************************************************************

/**
 * @update [Andréas Garcia] 15 Jan., 2019: change "concern" occurences to "concernedItem"
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\date\DatePicker;
use kartik\select2\Select2;

require_once '../config/config.php';

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="document-form well">
    <?php $form = ActiveForm::begin(); ?>
    
    <script>
        // On form submission disable creation button to prevent multiple document creation
        $(document).ready(function() {
           $("form#<?= $form->id ?>").submit(function() {
               $("form#<?= $form->id ?> button[type=submit]").attr("disabled", "disabled");
           });
        });
    </script>
    <?= $form->field($model, 'returnUrl')->hiddenInput(['readonly' => 'true'])->label(false); ?>
    
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>
    
    <?= $form->field($model, 'creator')->textInput(['maxlength' => true]); ?>
    
    <!-- Faire une dropdown list ?????-->
    <?= $form->field($model, 'language')->textInput(['maxlength' => true, 'placeholder' => 'fr']); ?>
    
    <?= $form->field($model, 'creationDate')->widget(DatePicker::className(), [
        'options' => ['placeholder' => 'Enter date creation document'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?php 
        if ($model->isNewRecord) {            
            echo $form->field($model, 'concernedItems')->widget(Select2::classname(),[
                'data' =>$this->params['currentConcernedItem'],
                'readonly' => true,
                'pluginOptions' => [                    
                    'multiple' => false,
                ],
            ]);
        }
    ?>
    
    <?= $form->field($model, 'documentType')->widget(Select2::classname(), [
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
                'maxFileSize'=>40000
            ]
            ]);
    } ?>
    
    <?= $form->field($model, 'comment')->textarea(['rows' => '5']) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii' , 'Create') : Yii::t('yii' , 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
    
</div>
