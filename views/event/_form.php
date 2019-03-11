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
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventPost;
use app\components\helpers\Vocabulary;
?>
<div class="event-form well">
    <?php 
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    
    foreach ($this->params['eventPossibleTypes'] as $eventPossibleType) {
        $eventPossibleTypes[$eventPossibleType] = Html::encode(Vocabulary::prettyUri($eventPossibleType));
    }
    ?>
    <?=
    $form->field($model, YiiEventModel::TYPE)->widget(Select2::classname(), [
        'data' => $eventPossibleTypes,
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>
    <?=
    $form->field($model, EventPost::DATE_WITHOUT_TIMEZONE)->widget(DateTimePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter event time')
        ],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => Yii::$app->params['dateTimeFormatDateTimePickerUserFriendly']
        ]
    ])
    ?>
    <?=
    $form->field($model, EventPost::CREATOR_TIMEZONE_OFFSET)->textInput([
        'maxlength' => true
    ]);
    ?>
    <script>
        window.onload = setCreatorTimezoneOffset();
        function setCreatorTimezoneOffset() {
            // getTimezoneOffset() returns UTC - localTimeZone. 
            // We want localTimeZone - UTC so we take the reciprocal value
            var offsetTotalInMinutes = -(new Date()).getTimezoneOffset();
            var offsetTotalInMinutesAbs = Math.abs(offsetTotalInMinutes);
            var offsetSign = offsetTotalInMinutesAbs === offsetTotalInMinutes ? "+" : "-";
            var offsetHours = Math.trunc(offsetTotalInMinutesAbs/60);
            var offsetHoursString = Math.abs(offsetHours) > 10 ? "" + offsetHours : "0" + offsetHours;
            var offsetMinutes = Math.abs(offsetTotalInMinutesAbs)%60;
            var offsetMinutesString = Math.abs(offsetMinutes) > 10 ? "" + offsetMinutes : "0" + offsetMinutes;
            var offsetInStandardFormat = offsetSign + offsetHoursString + ":" + offsetMinutesString;
            
            $("#eventpost-creatortimezoneoffset").val(offsetInStandardFormat);
        };
    </script>
    <?php
    foreach ($model->concernedItemsUris as $concernedItemUri) {
        $concernedItemsUris[] = [EventPost::CONCERNED_ITEMS_URIS => $concernedItemUri];
    }

    $dataProvider = new ArrayDataProvider([
        'allModels' => $concernedItemsUris,
        'pagination' => [
            'pageSize' => 10,
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            Yii::t('app', EventPost::CONCERNED_ITEMS_URIS)
        ],
    ]);
    ?>
    <?= $form->field($model, EventPost::DESCRIPTION)->textarea(['rows' => Yii::$app->params['textAreaRowsNumber']]) ?>

    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

