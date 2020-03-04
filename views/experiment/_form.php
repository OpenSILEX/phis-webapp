<?php

//**********************************************************************************************
//                                      _form.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  February, 2017
// Subject: formulaire de creation ou de modification d'une expérimentation
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YiiExperimentModel */
/* @var $form yii\widgets\ActiveForm */
?>
<script>
$(document).ready(function(){
    $("#experimentURI").tooltip();
    $("#experimentEndDate").tooltip();
});
</script>

<div class="experiment-form well">

    <?php $form = ActiveForm::begin(); ?>

    <?php 
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uri')->textInput([
                'readonly' => true, 
                'style' => 'background-color:#C4DAE7;',
            ]);
    }
    ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    
    <?php
        if ($model->isNewRecord) {
            echo $form->field($model, 'projects')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listProjects'],
                'options' => [
                    'placeholder' => 'Select projects ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        } else {
            echo $form->field($model, 'projects')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listProjects'],
                'options' => [
                    'value' => $this->params['listActualProjects'],
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        }
    ?>
    
    <?= $form->field($model, 'startDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => 'Enter date start', 
            'onChange' => 'updateURI()',
            'id' => 'experimentStartDate'],            
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?= $form->field($model, 'endDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => 'Enter date end',
            'id' => 'experimentEndDate',
            'data-toggle' => 'tooltip',
            'title' => 'Same as start date if unknown end date',
            'data-placement' => 'left'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>

    <?php
    $readonly = $model->isNewRecord ? false : true;
    echo $form->field($model, 'campaign')->textInput(
            ['maxlength' => true, 
             'id' => 'experimentCampaign', 
             'type' => 'number', 
             'placeholder' => '2017',
             'readonly' => $readonly,
             'onChange' => 'updateURI()',]) ?>

    <?php if ($model->isNewRecord) {
        echo $form->field($model, 'scientificSupervisorContacts')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listContacts'],
                'options' => [
                    'placeholder' => 'Select scientific supervisor ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } else {
        echo $form->field($model, 'scientificSupervisorContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'value' => $this->params['listActualScientificSupervisors'],
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    }
    ?>
    
    <?php if ($model->isNewRecord) {
        echo $form->field($model, 'technicalSupervisorContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'placeholder' => 'Select technical supervisor ...',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    } else {
        echo $form->field($model, 'technicalSupervisorContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'value' => $this->params['listActualTechnicalSupervisors'],
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    }
    ?>

    <?php
    $cropSpecies = null;
    if ($model->attributes[\app\models\yiiModels\YiiExperimentModel::CROP_SPECIES] != null) {
        $cropSpecies[] = $model->attributes[\app\models\yiiModels\YiiExperimentModel::CROP_SPECIES];
    }

    echo $form->field($model, 'cropSpecies')->widget(\kartik\select2\Select2::classname(), [
        'data' => $this->params['listSpecies'],
        'options' => ['placeholder' => Yii::t('app', 'Select species'),
            'multiple' => false,
            'value' => $cropSpecies],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]) ?>

    <?php if ($model->isNewRecord) {
            echo $form->field($model, 'groups')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listGroups'],
                'options' => [
                    'placeholder' => 'Select groups ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        } else {
            echo $form->field($model, 'groups')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listGroups'],
                'options' => [
                    'value' => $this->params['listActualGroups'],
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        }
    ?>
    <?= $form->field($model, 'objective')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
