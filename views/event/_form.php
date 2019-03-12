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
use app\controllers\EventController;
use app\components\helpers\Vocabulary;
?>
<div class="event-form well">
    <?php 
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    
    foreach ($this->params[EventController::EVENT_TYPES] as $eventPossibleType) {
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
    $form->field($model, EventPost::PROPERTY_HAS_PEST)->textInput([
        'maxlength' => true
    ]);
    ?>
    <?php
    $infrastructuresLabels = [];
    foreach ($this->params[EventController::INFRASTRUCTURES_DATA] as $infrastructure) {
        $infrastructuresLabels[$infrastructure[EventController::INFRASTRUCTURES_DATA_URI]] = $infrastructure[EventController::INFRASTRUCTURES_DATA_LABEL];
    }
    ?>
    <?=
    $form->field($model, EventPost::PROPERTY_FROM)->widget(Select2::classname(), [
        'data' => $infrastructuresLabels,
        'pluginOptions' => [
            'allowClear' => false,
        ]
    ]);    
    ?>
    <?=
    $form->field($model, EventPost::PROPERTY_TO)->widget(Select2::classname(), [
        'data' => $infrastructuresLabels,
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>
    <?php
    $infrastructuresTypes = [];
    foreach ($this->params[EventController::INFRASTRUCTURES_DATA] as $infrastructure) {
        $infrastructuresTypes[$infrastructure[EventController::INFRASTRUCTURES_DATA_URI]] = $infrastructure[EventController::INFRASTRUCTURES_DATA_TYPE];
    }
    ?>
    <?=
    $form->field($model, EventPost::PROPERTY_TYPE)->widget(Select2::classname(), [
        'data' => $infrastructuresTypes,
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
    
    <script>
        var selectClass = "select";

        var hasPestDiv = $('.field-eventpost-propertyhaspest');
        var fromDiv = $('.field-eventpost-propertyfrom');
        var toDiv = $('.field-eventpost-propertyto');
        var propertyTypeDiv = $('.field-eventpost-propertytype');

        var toSelect = toDiv.find(selectClass);
        var fromSelect = fromDiv.find(selectClass);
        var hasPestSelect = hasPestDiv.find(selectClass);
        var propertyTypeSelect = propertyTypeDiv.find(selectClass);

        var eventTypeSelect = $('#eventpost-rdftype');
            
        window.onload = function () {
            
            hidePropertyBlocs(hasPestDiv, fromDiv, toDiv);
            setEventTypeSelectOnChangeBehaviour(eventTypeSelect, hasPestDiv, fromDiv, toDiv);
            
            // Set right property type when the user select new property
            fromSelect.on('change', function (e) {
                setPropertyType(fromSelect.val());
            }); 
            toSelect.on('change', function (e) {
                setPropertyType(toSelect.val());
            }); 
            
            setCreatorTimezoneOffset();
        };
        
        function hidePropertyBlocs (hasPestDiv, fromDiv, toDiv) {
            hasPestDiv.hide();
            fromDiv.hide();
            toDiv.hide();
        }
        
        function setPropertyType (value) {
            propertyTypeSelect.val(value).trigger('change');;
        }
        
        function setEventTypeSelectOnChangeBehaviour (eventTypeSelect, hasPestDiv, fromDiv, toDiv) {
            // Show and hide property divs according to the type of event selected
            eventTypeSelect.on('change', function() {
                switch (this.value)  {
                    case "http://www.opensilex.org/vocabulary/oeev#MoveFrom":
                        hasPestDiv.hide();
                        fromDiv.show();
                        toDiv.hide();
                        break;
                    case "http://www.opensilex.org/vocabulary/oeev#MoveTo":
                        hasPestDiv.hide();
                        fromDiv.hide();
                        toDiv.show();
                        break;
                    default:
                        hidePropertyBlocs(hasPestDiv, fromDiv, toDiv);
                        break;
                }
            });
        }
        
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

<?php ActiveForm::end(); ?>
</div>

