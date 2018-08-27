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

/**
 * @action génère l'uri de l'expérimentation à partir de la date de début de l'expérimentation
 **/
function updateURI(){
    var campaign = $("#experimentCampaign").val();
    var uri = '<?php echo Yii::$app->params['baseURI']; ?>';
    var platformCode = '<?php echo Yii::$app->params['platformCode']; ?>';
    
    $("#experimentURI").val(uri + platformCode + campaign + "-?");
}  
</script>

<div class="experiment-form well">

    <?php $form = ActiveForm::begin(); ?>

    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, 'uri')->textInput([
                'maxlength' => true, 
                'readonly' => true, 
                'id' => 'experimentURI', 
                'value' => Yii::$app->params['baseURI'],
                'style' => 'background-color:#C4DAE7;',
                'data-toggle' => 'tooltip',
                'title' => 'Automatically generated',
                'data-placement' => 'left'
            ]);
    } else {
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

    <?= $form->field($model, 'field')->textInput(['maxlength' => true]) ?>

    <?php
    $readonly = $model->isNewRecord ? false : true;
    echo $form->field($model, 'campaign')->textInput(
            ['maxlength' => true, 
             'id' => 'experimentCampaign', 
             'type' => 'number', 
             'placeholder' => '2017',
             'readonly' => $readonly,
             'onChange' => 'updateURI()',]) ?>

    <?= $form->field($model, 'place')->textInput(['maxlength' => true]) ?>
    
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

    <?= $form->field($model, 'cropSpecies')->textInput() ?>
    
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
