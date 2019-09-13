<?php
//******************************************************************************
//                                _form.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventCreation;
use app\models\yiiModels\EventUpdate;
use app\models\yiiModels\EventAction;
use app\controllers\EventController;
use app\components\helpers\Vocabulary;
use app\components\widgets\handsontableInput\HandsontableInputWidget;
?>
<div class="event-form well">
    <?php 
    // Generate inputs name root and  inputs id root
    $eventClassWithNamespace = $model->isNewRecord ? EventCreation::class : EventUpdate::class;
    $eventInputsNameRoot = substr($eventClassWithNamespace, strrpos($eventClassWithNamespace, '\\') + 1);
    $eventInputsIdRoot = strtolower($eventInputsNameRoot);
    
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);

    // return URL after creation
    echo $form->field($model, EventAction::RETURN_URL)->hiddenInput(['readonly' => 'true'])->label(false);
    if ($model->isNewRecord) {
        echo $form->field($model, EventCreation::CREATOR)->hiddenInput(['readonly' => 'true'])->label(false);
    }

    if (!$model->isNewRecord) {
        echo $form->field($model, EventAction::URI)->textInput(['readonly' => true]);
    }
    
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
    $form->field($model, EventCreation::PROPERTY_HAS_PEST)->textInput([
        'maxlength' => true
    ]);
    ?>
    <?php
    $infrastructuresLabels = [];
    foreach ($this->params[EventController::INFRASTRUCTURES_DATA] as $infrastructure) {
        $infrastructuresLabels[$infrastructure[EventController::INFRASTRUCTURES_DATA_URI]] 
                = $infrastructure[EventController::INFRASTRUCTURES_DATA_LABEL];
    }
    ?>
    <?php
    $sensorsLabels = [];
    foreach ($this->params[EventController::SENSORS_DATA] as $sensor) {
        $sensorsLabels[$sensor[EventController::SENSOR_DATA_URI]] 
                = $sensor[EventController::SENSOR_DATA_LABEL];
    }
    ?>
    <?=
    $form->field($model, EventCreation::PROPERTY_FROM)->widget(Select2::classname(), [
        'data' => $infrastructuresLabels,
        'pluginOptions' => [
            'allowClear' => false,
        ]
    ]);    
    ?>
    <?=
    $form->field($model, EventCreation::PROPERTY_TO)->widget(Select2::classname(), [
        'data' => $infrastructuresLabels,
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>
    <?=
    $form->field($model, EventCreation::PROPERTY_ASSOCIATED_TO_A_SENSOR)->widget(Select2::classname(), [
        'data' => $sensorsLabels,
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>
    <?php
    $infrastructuresTypes = [];
    foreach ($this->params[EventController::INFRASTRUCTURES_DATA] as $infrastructure) {
        $infrastructuresTypes[$infrastructure[EventController::INFRASTRUCTURES_DATA_URI]] 
                = $infrastructure[EventController::INFRASTRUCTURES_DATA_TYPE];
    }
    ?>
    <?=
    $form->field($model, EventAction::PROPERTY_TYPE)->widget(Select2::classname(), [
        'data' => $infrastructuresTypes,
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);
    ?>
    <?php
    if (!$model->isNewRecord) {
        $options['value'] = $model->dateWithoutTimezone;
    }
    else
    {
        $options['placeholder'] = Yii::t('app', 'Enter event time');
    }
    ?>
    <?= $form->field($model, EventAction::DATE_WITHOUT_TIMEZONE)
            ->widget(DateTimePicker::className(), [
        'options' => $options,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => Yii::$app->params['dateTimeFormatDateTimePickerUserFriendly']
        ]
    ])
    ?>
    <?=
    $form->field($model, EventAction::DATE_TIMEZONE_OFFSET)->textInput([
        'maxlength' => true
    ]);
    ?>
    <?php 
    if ($model->isNewRecord) {
        echo $form->field(
                $model, 
                EventCreation::DESCRIPTION)->textarea(['rows' => Yii::$app->params['textAreaRowsNumber']]);
    }
    ?>
    <?php 
    $settings = 
        [
            'colHeaders' => ['URI'],
            'data' => $data,
            'rowHeaders' => true,
            'contextMenu' => true,
            'height'=> 200
        ];
    if (sizeof($model->concernedItems) > 8) {
          $settings = 
            [
                'colHeaders' => ['URI'],
                'data' => $data,
                'rowHeaders' => true,
                'contextMenu' => true,
                'height'=> 200
            ];
    } else {
         $settings = 
            [
                'colHeaders' => ['URI'],
                'data' => $data,
                'rowHeaders' => true,
                'contextMenu' => true,
            ];
    }
    if (sizeof($model->concernedItems) > 0) {
        foreach($model->concernedItems as $concernedItem) {
            $data[][0] = $concernedItem->uri;
        }
    }
    else {
        $data = [[]];
    }
    $settings['data'] = $data;
    
    if (!(sizeof($model->concernedItems) > 0)) {
        $settings['columns'] = [
            [
                'data' => 'URI',
                'type' => 'text',
                'placeholder' => 'http://www.opensilex.org/example/2019/o19000002'
            ]
        ];
    }
    ?>
    <?= HandsontableInputWidget::widget([
        'inputName' => $eventInputsNameRoot . "[" . EventCreation::CONCERNED_ITEMS_URIS . "][]",
        'settings' => $settings
    ]) ?>

    <div class="form-group">
    <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), 
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) 
    ?>
    </div>

    <script>
        var selectClass = "select";

        var hasPestDiv = $('div[class*="propertyhaspest"]');
        var fromDiv = $('div[class*="propertyfrom"]');
        var toDiv = $('div[class*="propertyto"]');
        var propertyTypeDiv = $('div[class*="propertytype"]');
        var associateToASensorDiv = $('div[class*="propertyassociatedtoasensor"]');

        var toSelect = toDiv.find(selectClass);
        var fromSelect = fromDiv.find(selectClass);
        var hasPestSelect = hasPestDiv.find(selectClass);
        var propertyTypeSelect = propertyTypeDiv.find(selectClass);
        var associateToASensorSelect = associateToASensorDiv.find(selectClass);

        var typeSelect = $('select[id*="rdftype"]');
            
        var dateOffsetInput = $('input[id*="datetimezoneoffset"]');
            
        hidePropertyBlocs();
            
        setEventTypeSelectOnChangeBehaviour();
        typeSelect.trigger('change');

        // Set right property type when the user select new property
        fromSelect.on('change', function (e) {
            setPropertyType(fromSelect.val());
        }); 
        toSelect.on('change', function (e) {
            setPropertyType(toSelect.val());
        }); 
        associateToASensorSelect.on('change', function (e) {
            setPropertyType(associateToASensorSelect.val());
        }); 
        
        
        if(!dateOffsetInput.val() || dateOffsetInput.val() === "") { // if event creation
            setDateTimezoneOffsetWithUserDefaultOne();
        }
        
        /**
         * Hides property blocs.
         */
        function hidePropertyBlocs () {
            hasPestDiv.hide();
            fromDiv.hide();
            toDiv.hide();
            propertyTypeDiv.hide();
            associateToASensorDiv.hide();
        }
        
        /**
         * Sets property type.
         */
        function setPropertyType (value) {
            propertyTypeSelect.val(value).trigger('change');;
        }
        
        
        /**
         * Sets behaviour on the event type select.
         */
        function setEventTypeSelectOnChangeBehaviour () {
            // Show and hide property divs according to the type of event selected
            typeSelect.on('change', function() {
                switch (this.value)  {
                    case "http://www.opensilex.org/vocabulary/oeev#MoveFrom":
                        hasPestDiv.hide();
                        fromDiv.show();
                        toDiv.hide();
                        associateToASensorDiv.hide();
                        break;
                    case "http://www.opensilex.org/vocabulary/oeev#MoveTo":
                        hasPestDiv.hide();
                        fromDiv.hide();
                        toDiv.show();
                        associateToASensorDiv.hide();
                        break;
                    case "http://www.opensilex.org/vocabulary/oeev#AssociatedToASensor":
                        hasPestDiv.hide();
                        fromDiv.hide();
                        toDiv.hide();
                        associateToASensorDiv.show();
                        break;
                    default:
                        hidePropertyBlocs(hasPestDiv, fromDiv, toDiv, associateToASensorDiv);
                        break;
                }
            });
        }
        
        /**
         * Sets the user's timezone offset.
         */
        function setDateTimezoneOffsetWithUserDefaultOne() {
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
            
            dateOffsetInput.val(offsetInStandardFormat);
        };
    </script>

    <?php ActiveForm::end(); ?>
    </div>
</div>