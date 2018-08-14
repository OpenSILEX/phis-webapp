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
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */
/* @var $sensorsTypes array */
/* @var $sensorsUris array */
/* @var $users array */
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
        
        //1.1 get the rdfType of the uri
        $.ajax({
           url: '<?= Url::to(['sensor/get-sensors-uri-by-rdf-type']) ?>',
           type: 'GET',
           data: 'uri=' + $('#uri').val(),
           datatype: 'json'
        }).done(function(data) {
            
            //1.2 update the rdfType field and show the right form
            rdfType = JSON.parse(data);
            
            var options = "<option value=\"\"></option>";
            
            //3. update the list
            if (sensors !== null) {
                for (i = 0; i < sensors.length; i++) {
                    options += "<option value=\"" + sensors[i].uri + "\">" + sensors[i].label + "</option>";
                }
            }
            
            $("#uri").html(options);
        });
        
        if (rdfType === "Camera" || rdfType === "HemisphericalCamera"
                 || rdfType === "HyperspectralCamera" || rdfType === "MultispectralCamera"
                 || rdfType === "RGBCamera" || rdfType === "TIRCamera") {
            $('#camera').show();
            $('#characterizeButton').show();
            $("#lidar").hide();
            $('#spectrometer').hide();
            $('#noCharacterization').hide();
            
            if (rdfType === "MultispectralCamera") {
                $('#wavelength').show();
                $('#lens').hide();
            } 
            if (rdfType === "RGBCamera" || rdfType === "TIRCamera") {
                $('#lens').show();
            }
            if (rdfType === "TIRCamera") {
                $('#waveband').show();
            }
        } else if (rdfType === "LiDAR") {
            $("#lidar").show();
            $('#characterizeButton').show();
            $('#spectrometer').hide();
            $('#noCharacterization').hide();
            $('#wavelength').hide();
            $('#lens').hide();
        } else if (rdfType === "Spectrometer") {
            $("#lidar").hide();
            $('#characterizeButton').show();
            $('#spectrometer').show();
            $('#noCharacterization').hide();
            $('#wavelength').hide();
            $('#lens').hide();
        } else {
            $('#noCharacterization').show();
            $('#characterizeButton').hide();
        }        
    }
</script>

<div class="characterize-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'rdfType')->widget(Select2::classname(), [
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
 
    <?= $form->field($model, 'uri')->widget(Select2::classname(), [
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
        <?= Html::label(Yii::t('app', 'Height') . ' (pixels)', 'height') ?>
        <?= Html::textInput('height', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Width') . ' (pixels)', 'width') ?>
        <?= Html::textInput('width', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Pixel Size') . ' (µm)', 'pixelSize') ?>
        <?= Html::textInput('pixelSize', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="wavelength" style="display:none">
        <table class="table table-hover">
            <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col">1</th>
              <th scope="col">2</th>
              <th scope="col">3</th>
              <th scope="col">4</th>
              <th scope="col">5</th>
              <th scope="col">6</th>
            </tr>
            <tbody>
                <tr>
                  <th scope="row"><?= Yii::t('app', 'Wavelength') ?> (nm)</th>
                  <td><?= Html::textInput('wavelength1', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength2', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength3', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength4', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength5', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength6', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                </tr>
                <tr>
                  <th scope="row"><?= Yii::t('app', 'Focal Length') ?> (nm)</th>
                  <td><?= Html::textInput('focalLength1', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength2', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength3', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength4', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength5', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength6', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                </tr>
                <tr>
                  <th scope="row"><?= Yii::t('app', 'Attenuator Filter') ?></th>
                  <td><?= Html::textInput('attenuatorFilter1', null, ['class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('attenuatorFilter2', null, ['class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('attenuatorFilter3', null, ['class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('attenuatorFilter4', null, ['class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('attenuatorFilter5', null, ['class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('attenuatorFilter6', null, ['class' => 'form-control']); ?></td>
                </tr>
            </tbody>
          </thead>
        </table>
    </div>
    
    <div id="waveband" style="display:none">
        <?= Html::label(Yii::t('app', 'Waveband') . ' (nm)', 'waveband') ?>
        <?= Html::textInput('waveband', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="lens" style="display:none">
       
        <h3><?= Yii::t('app', 'Lens') ?></h3>
        <hr>
        
        <?= Html::label(Yii::t('app', 'Label'), 'lensLabel') ?>
        <?= Html::textInput('lensLabel', null, ['class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Brand'), 'lensBrand') ?>
        <?= Html::textInput('lensBrand', null, ['class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Person In Charge'), 'lensPersonInCharge') ?>
        <?= Select2::widget([
            'name' => 'lensPersonInCharge',
            'data' => $users,
            'options' => [
                'id' => 'lensPersonInCharge',
                'multiple' => false,
                'prompt' => ''],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); ?>
        
        <?= Html::label(Yii::t('app', 'In Service Date'), 'lensInServiceDate') ?>
        <?= DatePicker::widget([
            'name' => 'lensInServiceDate',
            'options' => [
                'placeholder' => 'Enter in service date', 
                'id' => 'lensInServiceDate'],            
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>
        
        <?= Html::label(Yii::t('app', 'Focal Length') . ' (mm)', 'lensFocalLength') ?>
        <?= Html::textInput('lensFocalLength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Aperture') . ' (fnumber)', 'lensAperture') ?>
        <?= Html::textInput('lensAperture', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="lidar" style="display:none">
        <?= Html::label(Yii::t('app', 'Wavelength') . ' (nm)', 'wavelength') ?>
        <?= Html::textInput('wavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Scanning Angular Range') . ' (°)', 'scanningAngularRange') ?>
        <?= Html::textInput('scanningAngularRange', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Scan Angular Resolution') . ' (°)', 'scanAngularResolution') ?>
        <?= Html::textInput('scanAngularResolution', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spot width') . ' (°)', 'spotWidth') ?>
        <?= Html::textInput('spotWidth', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spot height') . ' (°)', 'spotHeight') ?>
        <?= Html::textInput('spotHeight', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="spectrometer" style="display:none">
        <?= Html::label(Yii::t('app', 'Half Field Of View') . ' (°)', 'halfFieldOfView') ?>
        <?= Html::textInput('halfFieldOfView', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Minimum Wavelength') . ' (°)', 'minWavelength') ?>
        <?= Html::textInput('minWavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Maximum Wavelength') . ' (°)', 'maxWavelength') ?>
        <?= Html::textInput('maxWavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spectral Sampling Interval'), 'spectralSamplingInterval') ?>
        <?= Html::textInput('spectralSamplingInterval', null, ['class' => 'form-control']); ?>
    </div>
    
    <div id="noCharacterization" style="display:none">
        <p>The selected sensor cannot be characterized. Please select another sensor among cameras (all camera types : RGB, multispectral, etc.), spectrometers and LiDAR.</p>
    </div>
    
    <br/>
    <div class="form-group" id="characterizeButton">
        <?= Html::submitButton(Yii::t('yii', 'Characterize Sensor'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>