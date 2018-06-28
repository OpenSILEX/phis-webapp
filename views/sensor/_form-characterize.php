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
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */
/* @var $sensorsTypes array */
/* @var $sensorsUris array */
?>
<script>
    /**
     * get the rdfType of the input rdfType and update the uri list with the 
     * list of the sensor's uris corresponding to the rdfType
     * @returns {undefined}
     */
    function updateSensorsUris() {
        //1. get the rdfType
        var rdfType = $('#rdfType').val();
        
        //2. get the sensors
        $.ajax({
           url: '<?= Url::to(['sensor/get-sensors-uri-by-rdf-type']) ?>',
           type: 'GET',
           data: 'rdfType=' + escape(rdfType),
           datatype: 'json'
        }).done(function(data) {
            sensors = JSON.parse(data);
            
            var options = "<option value=\"\"></option>";
            
            //3. update the list
            if (sensors !== null) {
                for (i = 0; i < sensors.length; i++) {
                    options += "<option value=\"" + sensors[i].uri + "\">" + sensors[i].label + "</option>";
                }
            }
            
            $("#uri").html(options);
        });
    }
    
    /**
     * show the characterization form for the sensor. 
     * The form showed depends on the sensor rdfType.
     * The form's labels are updated by querying the web service
     * @returns {undefined}     
     */
    function showForm() {
        //1. hide all the forms
        $('.characterize-attributes').hide();
        //1. show the right form
        //SILEX:warning 
        //for the first test, I use the rdfType of the form. In the next step 
        //I'll have to get the rdfType from the webservice
        //\SILEX:warning
        var rdfType = $('#rdfType').val().split("#")[1];
        
        if (rdfType === "Camera" || rdfType === "HemisphericalCamera"
                 || rdfType === "HyperspectralCamera" || rdfType === "MultispectralCamera"
                 || rdfType === "RGBCamera" || rdfType === "TIRCamera") {
            $('#camera').show();
        }
        
        if (rdfType === "MultispectralCamera") {
            $('#wavelength').show();
        }
        
        if (rdfType === "RGBCamera" || rdfType === "TIRCamera") {
            $('#lens').show();
        }
        
        if (rdfType === "LiDAR") {
            $("#lidar").show();
        }
        
        if (rdfType === "Spectrometer") {
            $('#spectrometer').show();
        }
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
 
    <?= $form->field($model, 'uri')->widget(\kartik\select2\Select2::classname(), [
                    'data' => $sensorsUris,
                    'size' => \kartik\select2\Select2::MEDIUM,
                    'options' => [
                                  'onChange' => 'showForm();',
                                  'id' => 'uri',
                                  'multiple' => false,
                                  'prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
    
    
    <div class="characterize-attributes" id="camera" style="display:none">
        <?= Html::label(Yii::t('app', 'Height') . '(pixels)', 'height') ?>
        <?= Html::textInput('height', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Width') . '(pixels)', 'width') ?>
        <?= Html::textInput('width', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Pixel Size') . '(µm)', 'pixelSize') ?>
        <?= Html::textInput('pixelSize', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div class="wavelength" style="display:none">
        TODO
    </div>
    
    <div id="lens" style="display:none">
        <?= Html::label(Yii::t('app', 'Focal Length') . '(mm)', 'focalLength') ?>
        <?= Html::textInput('focalLength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Aperture') . '(fnumber)', 'aperture') ?>
        <?= Html::textInput('aperture', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Brand'), 'brand') ?>
        <?= Html::textInput('brand', null, ['class' => 'form-control']); ?>
    </div>
    
    <div id="lidar" style="display:none">
        <?= Html::label(Yii::t('app', 'Wavelength') . '(nm)', 'wavelength') ?>
        <?= Html::textInput('wavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Scanning Angular Range') . '(°)', 'scanningAngularRange') ?>
        <?= Html::textInput('scanningAngularRange', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Scan Angular Resolution') . '(°)', 'scanAngularResolution') ?>
        <?= Html::textInput('scanAngularResolution', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spot width') . '(°)', 'spotWidth') ?>
        <?= Html::textInput('spotWidth', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spot height') . '(°)', 'spotHeight') ?>
        <?= Html::textInput('spotHeight', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="spectrometer" style="display:none">
        <?= Html::label(Yii::t('app', 'Half Field Of View') . '(°)', 'halfFieldOfView') ?>
        <?= Html::textInput('halfFieldOfView', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Minimum Wavelength') . '(°)', 'minWavelength') ?>
        <?= Html::textInput('minWavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Maximum Wavelength') . '(°)', 'maxWavelength') ?>
        <?= Html::textInput('maxWavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spectral Sampling Interval'), 'spectralSamplingInterval') ?>
        <?= Html::textInput('spectralSamplingInterval', null, ['class' => 'form-control']); ?>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii', 'Characterize Sensor'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>