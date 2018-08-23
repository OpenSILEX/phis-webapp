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
    var rdfType = null;
    /**
     * Get the rdfType of the input rdfType and update the uri list with the 
     * list of the sensor's uris corresponding to the rdfType.
     */
    function updateSensorsUris() {
        //1. Get the rdfType
        var rdfTypeSensor = $('#rdfType').val();
        
        //2. Get the sensors
        $.ajax({
           url: '<?= Url::to(['sensor/get-sensors-uri-by-rdf-type']) ?>',
           type: 'GET',
           data: 'rdfType=' + escape(rdfTypeSensor),
           datatype: 'json'
        }).done(function(data) {
            sensors = JSON.parse(data);
            
            var options = "<option value=\"\"></option>";
            
            //3. Update the list
            if (sensors !== null) {
                for (i = 0; i < sensors.length; i++) {
                    options += "<option value=\"" + sensors[i].uri + "\">" + sensors[i].label + "</option>";
                }
            }
            
            $("#uri").html(options);
        });
    }
    
    /**
     * Show the characterization form for the sensor. 
     * The form showed depends on the sensor rdfType.
     * The form's labels are updated by querying the web service.  
     */
    function showForm() {
        //1. Show the right form        
        //1.1 Get the rdfType of the uri
        $.ajax({
           url: '<?= Url::to(['sensor/get-sensors-type-by-uri']) ?>',
           type: 'GET',
           data: 'uri=' + encodeURIComponent($('#uri').val()),
           datatype: 'json'
        }).done(function(data) {
            rdfType = JSON.parse(data);
            //All subtypes of cameras
            if (rdfType === "<?= Yii::$app->params["Camera"] ?>"
                 || rdfType ==="<?= Yii::$app->params["HemisphericalCamera"] ?>"
                 || rdfType === "<?= Yii::$app->params["HyperspectralCamera"] ?>"
                 || rdfType === "<?= Yii::$app->params["MultispectralCamera"] ?>"
                 || rdfType === "<?= Yii::$app->params["RGBCamera"] ?>"
                 || rdfType === "<?= Yii::$app->params["TIRCamera"] ?>") {
                $('#camera').show();
                $('#characterizeButton').show();
                $("#lidar").hide();
                $('#spectrometer').hide();
                $('#noCharacterization').hide();
                
                //Multispectral cameras
                if (rdfType === "<?= Yii::$app->params["MultispectralCamera"] ?>") {
                    $('#wavelengthMS').show();
                    $('#lens').hide();
                }
                //RGB and TIR Cameras
                if (rdfType === "<?= Yii::$app->params["RGBCamera"] ?>" 
                        || rdfType === "<?= Yii::$app->params["TIRCamera"] ?>") {
                    $('#lens').show();
                }
                //TIR Cameras
                if (rdfType === "<?= Yii::$app->params["TIRCamera"] ?>") {
                    $('#waveband').show();
                }
            } else if (rdfType === "<?= Yii::$app->params["LiDAR"] ?>") { //LiDAR
                $("#lidar").show();
                $('#characterizeButton').show();
                $('#spectrometer').hide();
                $('#noCharacterization').hide();
                $('#wavelengthMS').hide();
                $('#lens').hide();
            } else if (rdfType === "<?= Yii::$app->params["Spectrometer"] ?>") { //Spectrometer
                $("#lidar").hide();
                $('#characterizeButton').show();
                $('#spectrometer').show();
                $('#noCharacterization').hide();
                $('#wavelengthMS').hide();
                $('#lens').hide();
            } else { //Sensor which doesn't need to be characterized
                $('#noCharacterization').show();
                $('#characterizeButton').hide();
            }
        });
    }
    
    /**
     * Validates that the required fields for a camera sensor are filled.
     * List of the required params : height, width, pixelSize
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing
     */
    function validateCamera() {
       return ($('#height').val() === null || $('#height').val() === "")
            && ($('#width').val() === null || $('#width').val() === "")
            && ($('#pixelSize').val() === null || $('#pixelSize').val() === "");
    }
    
    /**
     * Validates that the required fields for a multispectral camera sensor are filled.
     * List of the required params : wavelength, focalLength, attenuatorFilter x6
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing    
     */
    function validateMultispectralCamera() {
        //1. Check wavelenght
        if (($('#wavelength1').val() === null || $('#wavelength1').val() === "")
                || ($('#wavelength2').val() === null || $('#wavelength2').val() === "")
                || ($('#wavelength3').val() === null || $('#wavelength3').val() === "")
                || ($('#wavelength4').val() === null || $('#wavelength4').val() === "")
                || ($('#wavelength5').val() === null || $('#wavelength5').val() === "")
                || ($('#wavelength6').val() === null || $('#wavelength6').val() === "")) {
            return false;
        }
        
        //2. Check focalLength
        if (($('#focalLength1').val() === null || $('#focalLength1').val() === "")
                || ($('#focalLength2').val() === null || $('#focalLength2').val() === "")
                || ($('#focalLength3').val() === null || $('#focalLength3').val() === "")
                || ($('#focalLength4').val() === null || $('#focalLength4').val() === "")
                || ($('#focalLength5').val() === null || $('#focalLength5').val() === "")
                || ($('#focalLength6').val() === null || $('#focalLength6').val() === "")) {
            return false;
        }
        
        //3. Check attenuatorFilter
        if (($('#attenuatorFilter1').val() === null || $('#attenuatorFilter1').val() === "")
                || ($('#attenuatorFilter2').val() === null || $('#attenuatorFilter2').val() === "")
                || ($('#attenuatorFilter3').val() === null || $('#attenuatorFilter3').val() === "")
                || ($('#attenuatorFilter4').val() === null || $('#attenuatorFilter4').val() === "")
                || ($('#attenuatorFilter5').val() === null || $('#attenuatorFilter5').val() === "")
                || ($('#attenuatorFilter6').val() === null || $('#attenuatorFilter6').val() === "")) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validates that the required fields for a lens are filled.
     * List of the required params : lensLabel, lensBrand, lensPersonInCharge,
     *                               lensInServiceDate, lensFocalLength, lensAperture
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing
     */
    function validateLens() {
        if (($('#lensLabel').val() === null || $('#lensLabel').val() === "")
                || ($('#lensBrand').val() === null || $('#lensBrand').val() === "")
                || ($('#lensPersonInCharge').val() === null || $('#lensPersonInCharge').val() === "")
                || ($('#lensInServiceDate').val() === null || $('#lensInServiceDate').val() === "")
                || ($('#lensFocalLength').val() === null || $('#lensFocalLength').val() === "")
                || ($('#lensAperture').val() === null || $('#lensAperture').val() === "")) {
            return false;
        }
        return true;
    }
    
    /**
     * Validates that the required fields for a TIR Camera are filled.
     * List of the required params : waveband
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing   
     */
    function validateTIRCamera() {
        return ($('#waveband').val() !== null || $('#waveband').val() !== "");
    }
    
    /**
     * Validates that the required fields for a LiDAR are filled.
     * List of the required params : wavelength, scanningAngularRange
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing   
     */
    function validateLiDAR() {        
        if (($('#wavelength').val() === null || $('#wavelength').val() === "")
                || ($('#scanningAngularRange').val() === null || $('#scanningAngularRange').val() === "")) {
            return false;
        }
        return true;
    }
    
    /**
     * Validates that the required fields for a Spectrometer are filled.
     * List of the required params : halfFieldOfView, minWavelength, 
     *                               maxWavelength, spectralSamplingInterval
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing   
     */
    function validateSpectrometer() {
        if (($('#halfFieldOfView').val() === null || $('#halfFieldOfView').val() === "")
                || ($('#minWavelength').val() === null || $('#minWavelength').val() === "")
                || ($('#maxWavelength').val() === null || $('#maxWavelength').val() === "")
                || ($('#spectralSamplingInterval').val() === null || $('#spectralSamplingInterval').val() === "")) {
            return false;
        }
        return true;
    }
    
    /**
     * Validate that the required fields of the form are filled 
     * @returns {Boolean} true if the required field are filled
     *                    false if required fields are missing
     */
    function validateRequiredFields() {        
        // Validate required fields
        var validation = true;
        if (rdfType === "<?= Yii::$app->params["Camera"] ?>"  
                || rdfType === "<?= Yii::$app->params["HemisphericalCamera"] ?>"
                || rdfType === "<?= Yii::$app->params["HyperspectralCamera"] ?>"
                || rdfType === "<?= Yii::$app->params["MultispectralCamera"] ?>"
                || rdfType === "<?= Yii::$app->params["RGBCamera"] ?>"
                || rdfType === "<?= Yii::$app->params["TIRCamera"] ?>") {
            if (!validateCamera()) {
                validation = false;
            }

            if (rdfType === "<?= Yii::$app->params["MultispectralCamera"] ?>") {
                if (!validateMultispectralCamera()) {
                    validation = false;
                }
            } 
            if (rdfType === "<?= Yii::$app->params["RGBCamera"] ?>") {
                if (!validateTIRorRGBCamera() || !validateLens()) {
                    validation = false;
                }
            }
            if (rdfType === "<?= Yii::$app->params["TIRCamera"] ?>") {
                if (!validateTIRCamera()) {
                    validation = false;
                }
            }
        } else if (rdfType === "<?= Yii::$app->params["LiDAR"] ?>") {
            if (!validateLiDAR()) {
                validation = false;
            }
        } else if (rdfType === "<?= Yii::$app->params["Spectrometer"] ?>") {
            if (!validateSpectrometer()) {
                validation = false;
            }
        }
        
        if (!validation) {
            alert("<?= Yii::t('app/messages', 'Some required fields are missings') ?>");
        }
        
        return validation;
    }
</script>

<div class="characterize-form well" onSubmit="return validateRequiredFields();">
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

        <?= Html::label(Yii::t('app', 'Height') . ' (pixels) <font color="red">*</font>', 'height') ?>
        <?= Html::textInput('height', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Width') . ' (pixels) <font color="red">*</font>', 'width') ?>
        <?= Html::textInput('width', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Pixel Size') . ' (µm) <font color="red">*</font>', 'pixelSize') ?>
        <?= Html::textInput('pixelSize', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="wavelengthMS" style="display:none">
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
                  <th scope="row"><?= Yii::t('app', 'Wavelength') ?> (nm) <font color="red">*</font></th>
                  <td><?= Html::textInput('wavelength1', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength2', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength3', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength4', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength5', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('wavelength6', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                </tr>
                <tr>
                  <th scope="row"><?= Yii::t('app', 'Focal Length') ?> (nm) <font color="red">*</font></th>
                  <td><?= Html::textInput('focalLength1', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength2', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength3', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength4', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength5', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                  <td><?= Html::textInput('focalLength6', null, ['type' => 'number', 'class' => 'form-control']); ?></td>
                </tr>
                <tr>
                  <th scope="row"><?= Yii::t('app', 'Attenuator Filter') ?> <font color="red">*</font></th>
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
        <?= Html::label(Yii::t('app', 'Waveband') . ' (nm) <font color="red">*</font>', 'waveband') ?>
        <?= Html::textInput('waveband', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="lens" style="display:none">
       
        <h3><?= Yii::t('app', 'Lens') ?> <font color="red">*</font></h3>
        <hr>
        
        <?= Html::label(Yii::t('app', 'Label') . ' <font color="red">*</font>', 'lensLabel') ?>
        <?= Html::textInput('lensLabel', null, ['class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Brand') . ' <font color="red">*</font>', 'lensBrand') ?>
        <?= Html::textInput('lensBrand', null, ['class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Person In Charge') . ' <font color="red">*</font>', 'lensPersonInCharge') ?>
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
        
        <?= Html::label(Yii::t('app', 'In Service Date') . ' <font color="red">*</font>', 'lensInServiceDate') ?>
        <?= DatePicker::widget([
            'name' => 'lensInServiceDate',
            'options' => [
                'placeholder' => Yii::t('app/messages','Enter in service date'), 
                'id' => 'lensInServiceDate'],            
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>
        
        <?= Html::label(Yii::t('app', 'Focal Length') . ' (mm)  <font color="red">*</font>', 'lensFocalLength') ?>
        <?= Html::textInput('lensFocalLength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Aperture') . ' (fnumber)  <font color="red">*</font>', 'lensAperture') ?>
        <?= Html::textInput('lensAperture', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="lidar" style="display:none">
        <?= Html::label(Yii::t('app', 'Wavelength') . ' (nm)  <font color="red">*</font>', 'wavelength') ?>
        <?= Html::textInput('wavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Scanning Angular Range') . ' (°)  <font color="red">*</font>', 'scanningAngularRange') ?>
        <?= Html::textInput('scanningAngularRange', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Scan Angular Resolution') . ' (°)', 'scanAngularResolution') ?>
        <?= Html::textInput('scanAngularResolution', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spot width') . ' (°)', 'spotWidth') ?>
        <?= Html::textInput('spotWidth', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spot height') . ' (°)', 'spotHeight') ?>
        <?= Html::textInput('spotHeight', null, ['type' => 'number', 'class' => 'form-control']); ?>
    </div>
    
    <div id="spectrometer" style="display:none">

        <?= Html::label(Yii::t('app', 'Half Field Of View') . ' (°)  <font color="red">*</font>', 'halfFieldOfView') ?>
        <?= Html::textInput('halfFieldOfView', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Minimum Wavelength') . ' (nm)  <font color="red">*</font>', 'minWavelength') ?>
        <?= Html::textInput('minWavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Maximum Wavelength') . ' (nm)  <font color="red">*</font>', 'maxWavelength') ?>
        <?= Html::textInput('maxWavelength', null, ['type' => 'number', 'class' => 'form-control']); ?>
        
        <?= Html::label(Yii::t('app', 'Spectral Sampling Interval') . ' <font color="red">*</font>', 'spectralSamplingInterval') ?>
        <?= Html::textInput('spectralSamplingInterval', null, ['class' => 'form-control']); ?>
    </div>
    
    <div id="noCharacterization" style="display:none">
        <p> <?= Yii::t('app/messages', 'The selected sensor cannot be characterized. Please select another sensor among cameras (all camera types : RGB, multispectral, etc.), spectrometers and LiDAR.') ?></p>
    </div>
    
    <br/>
    <div class="form-group" id="characterizeButton">
        <?= Html::submitButton(Yii::t('yii', 'Characterize Sensor'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>