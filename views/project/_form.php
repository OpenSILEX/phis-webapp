<?php

//**********************************************************************************************
//                                       _form.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  May, 2017
// Subject: creation or update project's form
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YiiProjectModel */
/* @var $form yii\widgets\ActiveForm */
?>
<script>
$(document).ready(function(){
    $("#projectURI").tooltip();
    $("#projectAcronyme").tooltip();
    $("#projectName").tooltip();
    $("#projectFinancialName").tooltip();
    $("#projectKeywords").tooltip();
});

//replace spaces by "-"
function changeSpaces(text, idInput) {
    $("#" + idInput).val(text.replace(" ", '-'));
}
</script>

<div class="project-form well">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
         echo $form->field($model, 'uri')->textInput([
                'maxlength' => true,
                'readonly' => true,  
                'style' => 'background-color:#C4DAE7;', ]);
    }
    ?>
    
    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'shortname')->textInput([
            'maxlength' => true, 
            'id' => 'projectAcronyme', 
            'onkeyup' => 'changeSpaces(this.value, this.id);',
            'data-toogle' => 'tooltip', 
            'title' => Yii::t('app/messages','No spaces allowed. Used for the URI. Example : drops'), 
            'data-placement' => 'left']);
    } else {
        echo $form->field($model, 'shortname')->textInput(['maxlength' => true, 'readOnly' => true, 'style' => 'background-color:#C4DAE7;']);
    }
    ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'id' => 'projectName', 'data-toogle' => 'tooltip', 'title' => 'ex: DROught-tolerant yielding PlantS', 'data-placement' => 'left']) ?>
    
    <?php 
        echo $form->field($model, 'relatedProjects')->widget(\kartik\select2\Select2::classname(), [
                'data' =>$this->params['listProjects'],
                'options' => ['placeholder' => 'Select related project',
                              'multiple' => false],
                'pluginOptions' => [
                    'tags'=>true,
                    'allowClear' => true
                ]
            ]);
    ?>
    
    <?= $form->field($model, 'objective')->textInput(['maxlength' => true]) ?>
    
    <?php
        $financialValue = null;
        if ($model->financialFunding != null) {
            $financialValue[] = $model->financialFunding->uri;
        }
        
        echo $form->field($model, 'financialFunding')->widget(\kartik\select2\Select2::classname(), [
            'data' => $this->params['listFinancialFundings'],
            'options' => ['placeholder' => Yii::t('app', 'Select financial funding'),
                          'multiple' => false,
                            'value' => $financialValue],
            'pluginOptions' => [
                'tags'=>true,
                'allowClear' => true
            ]
    ]) ?>

    
    <?= $form->field($model, 'financialReference')->textInput(['maxlength' => true, 'id' => 'projectFinancialName', 'data-toogle' => 'tooltip', 'title' => 'Code contrat', 'data-placement' => 'left']) ?>
    
    <?= $form->field($model, 'startDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => ['placeholder' => 'Enter date start'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?= $form->field($model, 'endDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => ['placeholder' => 'Enter date end'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?php if ($model->isNewRecord) {
            echo $form->field($model, 'scientificContacts')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listContacts'],
                'options' => [
                    'placeholder' => 'Select scientific contact ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        } else {
             echo $form->field($model, 'scientificContacts')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listContacts'],
                'options' => [
                    'value' => $this->params['listActualScientificContacts'],
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        }
    ?>
    
    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, 'administrativeContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'placeholder' => 'Select administrative contact ...',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    } else {
        echo $form->field($model, 'administrativeContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'value' => $this->params['listActualAdministrativeContacts'],
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    }
    ?>
    
    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, 'projectCoordinatorContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'placeholder' => 'Select project coordinator ...',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); 
    } else {

        echo $form->field($model, 'projectCoordinatorContacts')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'value' => $this->params['listActualCoordinators'],
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); 
    }
    ?>

    <?= $form->field($model, 'homePage')->textInput(['maxlength' => true]) ?>
    
    <?php echo $form->field($model, 'keywords')->widget(\kartik\select2\Select2::classname(),[
            'data' => [],
            'options' => [
                'multiple' => true
            ],
            'pluginOptions' => [
                'tags' => true,
                'allowClear' => true
            ],
        ]); 
    ?>
    <?= $form->field($model, 'description')->textarea(['rows' => '6']) ?>
    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii' , 'Create') : Yii::t('yii' , 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>