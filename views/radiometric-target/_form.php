<?php

//******************************************************************************
//                                       _form.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 01 Oct, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\YiiRadiometricTargetModel */
/* @var $hideFiles boolean */
?>

<div class="radiometric-target-form well">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

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

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'serialNumber')->textInput(['maxlength' => true]) ?>

    <?=
    $form->field($model, 'inServiceDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter in service date'),
            'id' => 'rtInServiceDate'
        ],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ])
    ?>

    <?=
    $form->field($model, 'dateOfPurchase')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter date of purchase'),
            'id' => 'rtDateOfPurchase'
        ],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ])
    ?>

    <?=
    $form->field($model, 'dateOfLastCalibration')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter date of last calibration'),
            'id' => 'rtDateOfLastCalibration'
        ],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ])
    ?>

    <?=
    $form->field($model, 'personInCharge')->widget(\kartik\select2\Select2::classname(), [
        'data' => $this->params['listContacts'],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'material')->widget(\kartik\select2\Select2::classname(), [
        'data' => [
            'carpet' => Yii::t('app', 'Carpet'),
            'painting' => Yii::t('app', 'Painting'),
            'spectralon' => Yii::t('app', 'Spectralon')
        ],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>

    <script>
        function radiometricTargetChangeFigure(value) {
            if (value === 'circular') {
                $(".field-yiiradiometrictargetmodel-diameter").show();
                $(".field-yiiradiometrictargetmodel-width").hide();
                $(".field-yiiradiometrictargetmodel-length").hide();
            } else {
                $(".field-yiiradiometrictargetmodel-diameter").hide();
                $(".field-yiiradiometrictargetmodel-width").show();
                $(".field-yiiradiometrictargetmodel-length").show();

            }
        }

        $(document).ready(function () {
            radiometricTargetChangeFigure($('#yiiradiometrictargetmodel-shape').val())
        });
    </script>

    <?=
    $form->field($model, 'shape')->widget(\kartik\select2\Select2::classname(), [
        'data' => [
            'rectangular' => Yii::t('app', 'Rectangular'),
            'circular' => Yii::t('app', 'Circular')
        ],
        'pluginOptions' => [
            'allowClear' => false
        ],
        'pluginEvents' => [
            "change" => "function(event) { radiometricTargetChangeFigure(event.target.value)}",
        ]
    ]);
    ?>

    <?= $form->field($model, 'length')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'width')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'diameter')->textInput(['maxlength' => true]) ?>

    <?= Yii::t('app', 'A radiometric target can be described by the value of its coefficients to the bidirectional reflectance distribution function (see the BRDF '),
            Html::a(Yii::t('app', 'wikipedia page'), "https://en.wikipedia.org/wiki/Bidirectional_reflectance_distribution_function", ['target' => '_blank']),
            '):' ?>
    <div class="text-center">
    <?= Html::img('images/figures/radiometric-target_BRDF-equation.png', ["class"=>"img-thumbnail"]);?>
    </div>

    <?= $form->field($model, 'brdfP1')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brdfP2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brdfP3')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brdfP4')->textInput(['maxlength' => true]) ?>
    
    <!--
    <div class="alert alert-info" role="alert">
        <b><?= Yii::t('app/messages', 'File Rules')?> : </b>
        <table class="table table-hover" id="radiometric-target-csv-columns-desc">
            <tr>
                <th style="color:red">Yii::t('app/message', 'Acquisition date of the data') *</th>
                <td><p><?= Yii::t('app', 'Wavelength (nm)') ?> (format : YYYY-MM-DD) </p> </td>
            </tr>
             <tr class="dataset-variables">
                <th style="color:red">Value *</th>
                <td ><?= Yii::t('app/messages', 'Value') ?> (<?= Yii::t('app', 'Real Number') ?>)</td>
            </tr>
        </table>
    </div>

    
    <p><i>
         <?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Template'), \config::path()['basePath'] . '/documents/DatasetFiles/datasetTemplate.csv', ['id' => 'downloadDatasetTemplate']) ?>
    </i></p>
    -->
    
    <?php
    if (!$hideFiles) {
        echo $form->field($model, 'reflectanceFile')->widget(FileInput::classname(), [
            'options' => [
                'multiple' => false,
            ],
            'pluginOptions' => [
                'maxFileCount' => 1,
                'maxFileSize' => 2000
            ]
        ]);
    }
    ?>

    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

