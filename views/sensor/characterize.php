<?php

//******************************************************************************
//                                       characterize.php
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
/* @var $sensor app\models\YiiSensorModel */

$this->title = Yii::t('app', 'Characterize Sensor');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensor-create">
    <h1><?= Html::encode($this->title) ?> - <?= $sensor->label ?></h1>
    <script>
        $(document).ready(function() {
            $('.characterize-form > form').yiiActiveForm('init', []);
        })
        
        var rdfType = "<?= $sensor->rdfType ?>";
        var inputLabels = {};
        
        function addRequiredField(name, label) {
            $('.characterize-form > form').yiiActiveForm('add', {
                id: name,
                name: name,
                container: "#input-container-" + name,
                input: "[name=" + name + "]"
            });   
            
            inputLabels[name] = label;
        }    
        
        function addRequiredFieldGroup(groupName, label, count) {
            for (var i = 1; i <= count; i++) {
                var name = groupName + i;
                $('.characterize-form > form').yiiActiveForm('add', {
                    id: name,
                    name: name,
                    container: "#input-container-" + groupName,
                    input: "[name=" + name + "]"
                });   
                
                inputLabels[name] = label;
            }
        }            
        
        var requiredMessage = "<?= Yii::t('yii', '{attribute} cannot be blank.') ?>";
        
        function checkRequiredFields() {
            var emptyFields = false;
            
            for (var i = 0; i < arguments.length; i++) {
                var fieldName = arguments[i];
                
                var fieldValue = $('[name=' + fieldName + ']').val();
                var isEmpty = (fieldValue === null) || (fieldValue === "");
                
                emptyFields = emptyFields || isEmpty;
                console.log(fieldName, fieldValue, isEmpty)
                if (isEmpty) {
                    var message = requiredMessage.replace("{attribute}", inputLabels[fieldName]);
                    $('.characterize-form > form').yiiActiveForm('updateAttribute', fieldName, [message]);
                }
            }
            
            return !emptyFields;
        }
        
        function checkRequiredFieldGroup(groupName, count) {
            var emptyFields = false;
            var fieldName = "";
            
            for (var i = 1; i <= count; i++) {
                fieldName = groupName + i;
                
                var fieldValue = $('[name=' + fieldName + ']').val();
                var isEmpty = (fieldValue === null) || (fieldValue === "");
                
                emptyFields = emptyFields || isEmpty;
            }
            
            if (emptyFields) {
                for (var i = 1; i <= count; i++) {
                    fieldName = groupName + i;
                    var message = requiredMessage.replace("{attribute}", inputLabels[fieldName]);
                    $('.characterize-form > form').yiiActiveForm('updateAttribute', fieldName, [message]);
                }
            }
            
            return !emptyFields;
        }
        
        /**
         * Validates that the required fields for a camera sensor are filled.
         * List of the required params : height, width, pixelSize
         * @returns {Boolean} true if the required field are filled
         *                    false if required fields are missing
         */
        function validateCamera() {
           return checkRequiredFields("height", "width", "pixelSize");
        }

        /**
         * Validates that the required fields for a multispectral camera sensor are filled.
         * List of the required params : wavelength, focalLength, attenuatorFilter x6
         * @returns {Boolean} true if the required field are filled
         *                    false if required fields are missing
         */
        function validateMultispectralCamera() {
            var wavelengthValid = checkRequiredFieldGroup("wavelength", 6);
            var focalLengthValid = checkRequiredFieldGroup("focalLength", 6);
            var attenuatorFilterValid = checkRequiredFieldGroup("attenuatorFilter", 6);
            
            return wavelengthValid
                && focalLengthValid
                && attenuatorFilterValid;
        }

        /**
         * Validates that the required fields for a lens are filled.
         * List of the required params : lensLabel, lensBrand, lensPersonInCharge,
         *                               lensInServiceDate, lensFocalLength, lensAperture
         * @returns {Boolean} true if the required field are filled
         *                    false if required fields are missing
         */
        function validateLens() {
            return checkRequiredFields(
                "lensLabel", 
                "lensBrand", 
                "lensPersonInCharge",
                "lensInServiceDate",
                "lensFocalLength",
                "lensAperture"
            );            
        }

        /**
         * Validates that the required fields for a TIR Camera are filled.
         * List of the required params : waveband
         * @returns {Boolean} true if the required field are filled
         *                    false if required fields are missing
         */
        function validateTIRCamera() {
            var lensValidation = validateLens();
            return checkRequiredFields("waveband") && lensValidation;
        }

        /**
         * Validates that the required fields for a LiDAR are filled.
         * List of the required params : wavelength, scanningAngularRange
         * @returns {Boolean} true if the required field are filled
         *                    false if required fields are missing
         */
        function validateLiDAR() {
            return checkRequiredFields(
                "wavelength", 
                "scanningAngularRange",
                "scanAngularResolution",
                "spotWidth",
                "spotHeight"
            );                 
        }

        /**
         * Validates that the required fields for a Spectrometer are filled.
         * List of the required params : halfFieldOfView, minWavelength,
         *                               maxWavelength, spectralSamplingInterval
         * @returns {Boolean} true if the required field are filled
         *                    false if required fields are missing
         */
        function validateSpectrometer() {
            return checkRequiredFields(
                "halfFieldOfView", 
                "minWavelength",
                "maxWavelength",
                "spectralSamplingInterval"
            );                 
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
                    if (!validateLens()) {
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

            return validation;
        }
    </script>

    <?php 
        function createValidatedInput($name, $label, $type, $unit = null) {
            $toReturn = '<div id="input-container-' . $name . '" class="form-group">';
            $completeLabel = $label;
            if ($unit != null) {
                $completeLabel .=  ' (' . $unit . ') ';
            }
            $toReturn .= Html::label($completeLabel . '<font color="red">*</font>', $name, ['class' => 'control-label']);
            $toReturn .= Html::textInput($name, null, ['type' => $type, 'class' => 'form-control']);
            $toReturn .= '<div class="help-block"></div>';
            $toReturn .= <<<EOT
                <script>
                    $(document).ready(function() {
                        addRequiredField("$name", "$label");
                    })
                </script>
EOT;
            $toReturn .= '</div>';
            
            return $toReturn;
        }
        
        function createValidatedInputTableRow($name, $count, $label, $type, $unit = null, $extraText = "") {
            $toReturn = '<tr id="input-container-' . $name . '" class="form-group">';
            $completeLabel = $label;
            if ($unit != null) {
                $completeLabel .=  ' (' . $unit . ') ';
            }            
            $toReturn .= '<th scope="row">' . $completeLabel .'<font color="red">*</font>' . $extraText . '<div class="help-block"></div></th>';
            
            for ($i = 1; $i <= $count; $i++) {
                $toReturn .= '<td>' . Html::textInput($name . $i, null, ['type' => $type, 'class' => 'form-control', 'step' => 'any']) . '</td>';
            }
                        
            $toReturn .= <<<EOT
                <script>
                    $(document).ready(function() {
                        addRequiredFieldGroup("$name", "$label", $count);
                    })
                </script>
EOT;
                        
            $toReturn .= '</tr>';
            
            return $toReturn;
        }
    ?>
    
    <div class="characterize-form well" onSubmit="return validateRequiredFields();">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uri')->hiddenInput(['value'=> $sensor->uri])->label(false); ?>
        
        <?php if ($sensor->rdfType === Yii::$app->params["Camera"]
                 || $sensor->rdfType ===Yii::$app->params["HemisphericalCamera"]
                 || $sensor->rdfType === Yii::$app->params["HyperspectralCamera"]
                 || $sensor->rdfType === Yii::$app->params["MultispectralCamera"]
                 || $sensor->rdfType === Yii::$app->params["RGBCamera"]
                 || $sensor->rdfType === Yii::$app->params["TIRCamera"]): ?>
            <script>
         
            </script>
            <div class="characterize-attributes" id="camera">
                <?= createValidatedInput('height', Yii::t('app', 'Height'), 'number', 'pixels') ?>
                <?= createValidatedInput('width', Yii::t('app', 'Width'), 'number', 'pixels') ?>
                <?= createValidatedInput('pixelSize', Yii::t('app', 'Pixel Size'), 'number', 'µm') ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sensor->rdfType === Yii::$app->params["MultispectralCamera"]): ?>
            <div id="wavelengthMS">
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
                        <?= createValidatedInputTableRow('wavelength', 6, Yii::t('app', 'Wavelength'), 'number', 'nm'); ?>
                        <?= createValidatedInputTableRow('focalLength', 6, Yii::t('app', 'Focal Length'), 'number', 'mm'); ?>
                        <?= createValidatedInputTableRow('attenuatorFilter', 6, Yii::t('app', 'Attenuator Filter'), 'number', '%', ' [0 - 100]'); ?>
                    </tbody>
                  </thead>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if ($sensor->rdfType === Yii::$app->params["TIRCamera"]): ?>
            <div id="waveband">
                <?= createValidatedInput('waveband', Yii::t('app', 'Waveband'), 'number', 'nm') ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sensor->rdfType === Yii::$app->params["RGBCamera"]
                 || $sensor->rdfType === Yii::$app->params["TIRCamera"]): ?>
            <div id="lens">

                <h3><?= Yii::t('app', 'Lens') ?> <font color="red">*</font></h3>
                <hr>

                <?= createValidatedInput('lensLabel', Yii::t('app', 'Label'), 'text') ?>

                <?= createValidatedInput('lensBrand', Yii::t('app', 'Brand'), 'text') ?>

                <div id="input-container-lensPersonInCharge" class="form-group">
                    <?= Html::label(Yii::t('app', 'Person In Charge') . ' <font color="red">*</font>', 'lensPersonInCharge', ['class' => 'control-label']) ?>
                    <?= Select2::widget([
                        'name' => 'lensPersonInCharge',
                        'data' => $users,
                        'options' => [
                            'id' => 'lensPersonInCharge',
                            'multiple' => false,
                            'prompt' => ''],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],
                    ]); ?>
                    <div class="help-block"></div>
                    <script>
                        $(document).ready(function() {
                            addRequiredField("lensPersonInCharge", "Person In Charge");
                        })
                    </script>
                </div>
                
                <div id="input-container-lensInServiceDate" class="form-group">
                    <?= Html::label(Yii::t('app', 'In Service Date') . ' <font color="red">*</font>', 'lensInServiceDate', ['class' => 'control-label']) ?>
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
                    <div class="help-block"></div>
                    <script>
                        $(document).ready(function() {
                            addRequiredField("lensInServiceDate", "In Service Date");
                        })
                    </script>
                </div>


                <?= createValidatedInput('lensFocalLength', Yii::t('app', 'Focal Length'), 'number', 'mm') ?>

                <?= createValidatedInput('lensAperture', Yii::t('app', 'Aperture'), 'number', 'fnumber') ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sensor->rdfType === Yii::$app->params["LiDAR"]): ?>
            <div id="lidar">
                <?= createValidatedInput('wavelength', Yii::t('app', 'Wavelength'), 'number', 'nm') ?>

                <?= createValidatedInput('scanningAngularRange', Yii::t('app', 'Scanning Angular Range'), 'number', '°') ?>

                <?= createValidatedInput('scanAngularResolution', Yii::t('app', 'Scan Angular Resolution'), 'number', '°') ?>

                <?= createValidatedInput('spotWidth', Yii::t('app', 'Spot width'), 'number', '°') ?>

                <?= createValidatedInput('spotHeight', Yii::t('app', 'Spot height'), 'number', '°') ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sensor->rdfType === Yii::$app->params["Spectrometer"]): ?>        
            <div id="spectrometer">
                <?= createValidatedInput('halfFieldOfView', Yii::t('app', 'Half Field Of View'), 'number', '°') ?>

                <?= createValidatedInput('minWavelength', Yii::t('app', 'Minimum Wavelength'), 'number', 'nm') ?>

                <?= createValidatedInput('maxWavelength', Yii::t('app', 'Maximum Wavelength'), 'number', 'nm') ?>

                <?= createValidatedInput('spectralSamplingInterval', Yii::t('app', 'number', 'Spectral Sampling Interval')) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sensor->rdfType !== Yii::$app->params["Camera"]
                 && $sensor->rdfType !==Yii::$app->params["HemisphericalCamera"]
                 && $sensor->rdfType !== Yii::$app->params["HyperspectralCamera"]
                 && $sensor->rdfType !== Yii::$app->params["MultispectralCamera"]
                 && $sensor->rdfType !== Yii::$app->params["RGBCamera"]
                 && $sensor->rdfType !== Yii::$app->params["TIRCamera"]
                 && $sensor->rdfType !== Yii::$app->params["LiDAR"]
                 && $sensor->rdfType !== Yii::$app->params["Spectrometer"]): ?>
            <div id="noCharacterization">
                <p> <?= Yii::t('app/messages', 'The selected sensor cannot be characterized. Please select another sensor among cameras (all camera types : RGB, multispectral, etc.), spectrometers and LiDAR.') ?></p>
            </div>
        <?php else: ?>
            <br/>
            <?= Html::submitButton(Yii::t('app', 'Characterize'), ['class' => 'btn btn-success']) ?>
        <?php endif; ?>

        <?= Html::a(Yii::t('app', 'Back to sensor view'), ["sensor/view", "id" => $sensor->uri],['class' => 'btn btn-info']) ?>
            
        <?php ActiveForm::end(); ?>
    </div>

</div>