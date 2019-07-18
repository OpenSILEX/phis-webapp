<?php

//**********************************************************************************************
//                                       _form.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: creation or update group's form
//***********************************************************************************************

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YiiGroupModel */
/* @var $form yii\widgets\ActiveForm */

?>

<script>
$(document).ready(function(){
    $("#groupURI").tooltip();
    $("#groupName").tooltip();
    $('#groupOrganization').tooltip();
    $('#groupLaboratoryName').tooltip();
});

//forcer l'ecriture en majuscule dans le input et dans le post
function upperCase(text, idInput) {
    var textUpper = text.toUpperCase();
    $("#" + idInput).val(textUpper.replace(/-/g, '.'));
}  

//remplacement des espaces par des "."
function changeSpaces(text, idInput) {
    $("#" + idInput).val(text.replace(" ", '.'));
}

//le nom est composé de l'organisme - name
function updateName() {
    var organization = $('#groupOrganization').val();
    var name = organization + "-" + $('#groupName').val();

    $(".input-group-addon").text(organization + "-");
    
    updateURI(name);
}
</script>

<div class="project-form well">
    <?php $form = ActiveForm::begin(); ?>
    
    <?php 
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uri')->textInput([
            'maxlength' => true,
            'readonly' => true, 
            'style' => 'background-color:#C4DAE7;',]);
    }
?>
    
    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, 'name',[
                'inputOptions' => [
                    'placeholder' => 'LABORATORY-TEAM',
                    'onkeyup' => 'upperCase(this.value, this.id);changeSpaces(this.value, this.id);',
                    'onChange' => 'updateName();',
                    'id' => 'groupName'
                    ],
                'addon' => [
                    'prepend' => ['content'=> Yii::t('app', 'ORGANIZATION') . '-'],]]);
    } else {
        echo $form->field($model, 'name')->textInput(['readonly' => true, 'style' => 'background-color:#C4DAE7;']);
    }
    
    ?>
    
    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, 'organization')->textInput([
            'maxlength' => true,
            'onkeyup' => 'upperCase(this.value, this.id);changeSpaces(this.value, this.id);',
            'id' => 'groupOrganization',
            'onChange' => 'updateName();',
            'data-toogle' => 'tooltip',
            'title' => 'Used for the URI. Example : INRA', 
            'placeholder' => Yii::t('app', 'ORGANIZATION'),
            'data-placement' => 'left']);
    }
    ?>    
   
    <?= $form->field($model, 'level')->widget(\kartik\select2\Select2::classname(), [
                    'data' => [
                        'Guest' => Yii::t('app', 'Guest'),
                        'Owner' => Yii::t('app', 'Owner')],
                    'options' => ['placeholder' => 'Select level',
                                  'multiple' => false]
                ])?>
    
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    
    <?php 
    if ($model->isNewRecord) { 
        echo $form->field($model, 'users')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listUsers'],
                'options' => [
                    'placeholder' => 'Select members ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    } else {
        echo $form->field($model, 'users')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['listUsers'],
                'options' => [
                    'value' => $this->params['listActualMembers'],
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii' , 'Create') : Yii::t('yii' , 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>