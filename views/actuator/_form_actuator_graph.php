<?php

//******************************************************************************
//                                       _form_actuator_graph.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************


use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model app\models\yiiModels\DeviceDataSearch */
/* @var $variables array */
?>

<div class="actuator-visualisation well">
    <h3><?= Yii::t('app', 'Actuator Data Visualization') ?></h3>

    <div class="actuator-visualisation-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <?=
                $form->field($model, 'dateStart')->widget(\kartik\date\DatePicker::className(), [
                    'options' => ['placeholder' => 'Enter date start'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ])
                ?>
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'dateEnd')->widget(\kartik\date\DatePicker::className(), [
                    'options' => ['placeholder' => 'Enter date end'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ])
                ?>
            </div>
        </div>
        
        <p class="info-box">
            <?= Yii::t('app/messages', 'If no date are selected, visualization will render latest week of data found for actuator measured variables.'); ?>
            <br/>
            <?= Yii::t('app/messages', 'Measures displayed are limited to the 80 000 first results.'); ?>
        </p>
        
        <div class="form-group">
            <?= Html::Button(Yii::t('yii', 'Search'), ['class' => 'btn btn-primary', 'id' => 'actuator-data-search-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <script>
        $(document).ready(function(){
            // Define ajax url to get graph in javascript
            var ajaxUrl = '<?php echo Url::to(['actuator/search-data']) ?>';
            // Define actuatorUri in javascript
            var actuatorUri = '<?= $model->sensorURI ?>';
            // Define variables in javascript as a map uri -> label
            var variables = {};
            <?php foreach($variables as $uri => $label): ?>
                variables['<?= $uri ?>'] = '<?= $label ?>';
            <?php endforeach; ?>
            
            /**
             * Function to update all actuator data graph (1 by variable)
             */
            var refreshGraph = function() {

                // Create an array of request to make them all asynchronous
                var requests = [];
                // Foreach variable, call the ajax request to get the graph
                for(var uri in variables) {
                    requests.push(
                        $.post(
                            ajaxUrl,
                            {
                                "_csrf": $("input[name=_csrf]").val(),
                                "DeviceDataSearch": {
                                    "actuatorURI": actuatorUri,
                                    "variableURI": uri,
                                    "graphName": variables[uri],
                                    "dateStart": $("#actuatordatasearch-datestart").val(),
                                    "dateEnd": $("#actuatordatasearch-dateend").val()
                                }
                            }
                        )
                    );
                }
                
                // When all request are complete
                $.when.apply($, requests).then(function() {
                    // Concat all result if needed
                    var result = '';
                    if (requests.length > 1) {
                        for (var i = 0; i < arguments.length; i++) {
                            result += arguments[i][0];
                        }
                    } else {
                        result = arguments[0];
                    }
                    
                    // Replace current content with new graphs
                    $('#visualization-actuator-data').html(result);
                });
            };
            
            // On search click refresh the graph
            $('#actuator-data-search-button').click(function(event) {
                event.preventDefault();
                refreshGraph();
            });
            
            // Refresh the graph on startup
            refreshGraph();
        });
    </script>
    <div id="visualization-actuator-data"></div>
</div>
