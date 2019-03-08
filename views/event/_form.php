<?php
//******************************************************************************
//                                _form.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 01 Oct, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
?>
<div class="event-form well">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uri')->textInput([
            'readonly' => true,
            'style' => 'background-color:#C4DAE7;',
        ]);
    }
    ?>
    <?=
    $form->field($model, 'rdfType')->widget(Select2::classname(), [
        'data' => $this->params['eventPossibleTypes'],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>
    <?=
    $form->field($model, 'date')->widget(DateTimePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter event time')
        ],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => Yii::$app->params['dateTimeFormatDateTimePickerUserFriendly']
        ]
    ])
    ?>
    <?= $form->field($model, 'description')->textarea(['rows' => Yii::$app->params['textAreaRowsNumber']]) ?>

    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

