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

//remplacement des espaces par des "-"
function changeSpaces(text, idInput) {
    $("#" + idInput).val(text.replace(" ", '-'));
}

/**
 * @action ajoute l'acronyme du projet dans l'URI 
 **/
function updateURI(){
    var acronyme = $("#projectAcronyme").val();
    var uri = '<?php echo Yii::$app->params['baseURI']; ?>';
    
    $("#projectURI").val(uri + acronyme);
}    

</script>

<div class="project-form well">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'uri')->textInput([
                'maxlength' => true,
                'readonly' => true, 
                'value' => Yii::$app->params['baseURI'], 
                'id' => 'projectURI',  
                'style' => 'background-color:#C4DAE7;', 
                'data-toogle' => 'tooltip', 
                'title' => 'Automatically generated', 
                'data-placement' => 'left']);
    } else {
        echo $form->field($model, 'uri')->textInput([
                'maxlength' => true,
                'readonly' => true,  
                'style' => 'background-color:#C4DAE7;', ]);
    }
    
    ?>
    
    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'acronyme')->textInput([
            'maxlength' => true, 
            'id' => 'projectAcronyme', 
            'onkeyup' => 'changeSpaces(this.value, this.id);',
            'onChange' => 'updateURI()', 
            'data-toogle' => 'tooltip', 
            'title' => 'No spaces allowed. Used for the URI, example : drops', 
            'data-placement' => 'left']);
    } else {
        echo $form->field($model, 'acronyme')->textInput(['maxlength' => true, 'readOnly' => true, 'style' => 'background-color:#C4DAE7;']);
    }
    ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'id' => 'projectName', 'data-toogle' => 'tooltip', 'title' => 'ex: DROught-tolerant yielding PlantS', 'data-placement' => 'left']) ?>
    
    <?php if ($this->params['listProjects'] !== null) { 
            echo '<hr style="border-color : gray;">';
            echo '<h3>Subproject of <i>(optional)</i></h3>';

            echo $form->field($model, 'parentProject')->widget(\kartik\select2\Select2::classname(), [
                    'data' =>$this->params['listProjects'],
                    'options' => ['placeholder' => 'Select parent project',
                                  'multiple' => false],
                ]);
            
            
            echo $form->field($model, 'subprojectType')->widget(\kartik\select2\Select2::classname(), [
                    'data' => [
                        'Thesis' => 'Thesis',
                        'CDD' => 'CDD'],
                    'options' => ['placeholder' => 'Select subproject type',
                                  'multiple' => false],
                    'pluginOptions' => [
                        'tags'=>true,
                    ]
                ]);
            echo '<hr style="border-color : gray;">';
        }
    ?>
    
    <?= $form->field($model, 'objective')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'financialSupport')->widget(\kartik\select2\Select2::classname(), [
        'data' => [
                'ANR' => 'ANR',
                'INRA' => 'INRA',
                'Unit' => 'Unit',
                'European' => 'European',
                'International' => 'International'],
        'options' => ['placeholder' => 'Select financial support',
                      'multiple' => false],
        'pluginOptions' => [
            'tags'=>true,
        ]
    ]) ?>

    
    <?= $form->field($model, 'financialName')->textInput(['maxlength' => true, 'id' => 'projectFinancialName', 'data-toogle' => 'tooltip', 'title' => 'Code contrat', 'data-placement' => 'left']) ?>
    
    <?= $form->field($model, 'dateStart')->widget(\kartik\date\DatePicker::className(), [
        'options' => ['placeholder' => 'Enter date start'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?= $form->field($model, 'dateEnd')->widget(\kartik\date\DatePicker::className(), [
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
                'value' => $this->params['listActualProjectCoordinators'],
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); 
    }
    ?>

    <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true, 'id' => 'projectKeywords', 'data-toogle' => 'tooltip', 'title' => 'keyword1, keyword2, etc.', 'data-placement' => 'left']) ?>
    
    <?= $form->field($model, 'description')->textarea(['rows' => '6']) ?>
    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii' , 'Create') : Yii::t('yii' , 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>