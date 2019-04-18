<?php
//******************************************************************************
//                                _form.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventCreation;
use app\models\yiiModels\EventUpdate;
use app\models\yiiModels\EventAction;
use app\controllers\EventController;
use app\components\helpers\Vocabulary;
?>
<div class="event-form well">
    <?php 
    // generate inputs name root and  inputs id root
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
    // Concerned items
    
    // construct data provider
    $concernedItemsDataProviderModel = [];
    $i = 0;
    foreach ($model->concernedItems as $concernedItem) {
        $concernedItemsDataProviderModel[$i]->isNewRecord = $model->isNewRecord;
        $concernedItemsDataProviderModel[$i]->uri = $concernedItem->uri;
        $concernedItemsDataProviderModel[$i]->inputNameRoot = $eventInputsNameRoot;
        $i++;
    }
    $concernedItemsDataProvider = new ArrayDataProvider([
        'allModels' => $concernedItemsDataProviderModel,
        'pagination' => [
            'pageSize' => 10,
        ],
    ]);
    ?>
    <?= GridView::widget([
        'dataProvider' => $concernedItemsDataProvider,
        'columns' => [
            [
                'label' => Yii::t('app', "Concerned items URIs"),
                'value' => function($model){
        
                    // the root of the name of the input has to be the model class name                    
                    $concernedItemDiv = "<div class=\"form-group field-eventcreation-concerneditemuri\">";
                    $concernedItemDiv .= Html::textInput(
                            $model->inputNameRoot . "[" . EventAction::CONCERNED_ITEMS_URIS . "][]",
                            $model->uri, 
                            [
                                'class' => 'form-control',
                                'readonly'=> true
                            ]);
                    $concernedItemDiv .= "</div>";
                    return $concernedItemDiv;
                },
                'format' => 'raw'
            ]
        ],
    ]);
    ?>
    
    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, EventCreation::DESCRIPTION)->textarea(['rows' => Yii::$app->params['textAreaRowsNumber']]);
    }
    ?>

    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <script>
        var selectClass = "select";

        var hasPestDiv = $('div[class*="propertyhaspest"]');
        var fromDiv = $('div[class*="propertyfrom"]');
        var toDiv = $('div[class*="propertyto"]');
        var propertyTypeDiv = $('div[class*="propertytype"]');

        var toSelect = toDiv.find(selectClass);
        var fromSelect = fromDiv.find(selectClass);
        var hasPestSelect = hasPestDiv.find(selectClass);
        var propertyTypeSelect = propertyTypeDiv.find(selectClass);

        var typeSelect = $('select[id*="rdftype"]');
            
        var dateOffsetInput = $('input[id*="datetimezoneoffset"]');
            
        /**
         * Sets up the form on window's load.
         */
        window.onload = function () {
            
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
            if(!dateOffsetInput.val() || dateOffsetInput.val() === "") { // if event creation
                setDateTimezoneOffsetWithUserDefaultOne();
            }
        };
        
        /**
         * Hides property blocs.
         */
        function hidePropertyBlocs () {
            hasPestDiv.hide();
            fromDiv.hide();
            toDiv.hide();
            propertyTypeDiv.hide();
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
        
        /**
         * Sets the user's timezone oofset.
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

