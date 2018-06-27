<?php

//******************************************************************************
//                                       _form-characterize.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 27 juin 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  27 juin 2018
// Subject:
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */
/* @var $sensorsTypes array */
?>

<script>
    function updateSensorsUris() {
        //TODO
    }
</script>

<div class="characterize-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'rdfType')->widget(\kartik\select2\Select2::classname(), [
                    'data' =>$sensorsTypes,
                    'size' => \kartik\select2\Select2::MEDIUM,
                    'options' => [
                                  'onChange' => 'updateSensorsUris();',
                                  'id' => 'rdfType',
                                  'multiple' => false,
                                  'prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]); ?>
    
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii', 'Characterize Sensor'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>