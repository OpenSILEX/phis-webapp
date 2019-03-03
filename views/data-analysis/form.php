<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\grid\GridView;
?>

<div class="form">
    <h2> <?= $appConfiguration[$id]["label"] ?> </h2>
    <br>
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(["data-analysis/run-script/", "id" => $id]),
    ]);

    foreach ($model as $key => $attri) {
        if (isset($parameters[$key]['visibility']) && $parameters[$key]['visibility'] === "hidden") {
            echo $form->field($model, $key)->hiddenInput()->label(false);
        } else {
            if (isset($parameters[$key]['type'])) {

                switch ($parameters[$key]['type']) {
                    case 'string':
                        echo $form->field($model, $key)->label($parameters[$key]['label']);
                        break;
                    case 'date':
                        echo $form->field($model, $key)->widget(DatePicker::className(), [
                            'options' => [
                                'placeHolder' => date($parameters[$key]['dateValue'])],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => $parameters[$key]['format']
                            ]
                        ])->label($parameters[$key]['label']);
                        break;
                    case 'boolean':
                        echo $form->field($model, $key)->dropDownList([
                            false => 'Sans',
                            true => 'Avec'
                        ]);
                        break;
                    case 'list':

                        echo $form->field($model, $key)->widget(Select2::classname(), [
                            'data' => $parametersValues[$key],
                            'size' => Select2::MEDIUM,
                            'options' => [
                                'placeholder' => $parameters[$key]['label'],
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                "maximumSelectionLength" => 2,
                            ]
                        ])->label($parameters[$key]['label']);
                        break;
                    default:
                        break;
                }
            }
        }
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php
    ActiveForm::end();
    ?>
    <p class="alert alert-info"> Le résultat de votre script ou les erreurs produites s'afficheront ci-dessous.<p>
        <?php
        if (isset($plotConfigurations)) {
            foreach ($plotConfigurations as $plotConfiguration) {
                
            }
            // SILEX:test test to get config file from R package
            ?>

            <!-- Latest compiled and minified plotly.js JavaScript -->
            <!--<script src="https://cdn.plot.ly/plotly-1.39.0.min.js"></script>-->
            <!--<div id="myPlot"></div>-->
           <!--<script>
            <?php //  Url::to(["data-analysis/ajax-session-json-file-data",['filename'=>'plotlySchema','sessionId'=> $sessionId]]) ?>
    //               Plotly.d3.json("<?php // echo $plotConfigurationUrl ?>", function(err, plotSchema) {
    //////                    // assuming json is formatted as { "data": [/* */], "layout": {/* */} }
    //                     Plotly.newPlot('myPlot', plotSchema.data,plotSchema.layout);
    //              });
           </script>-->
            <?php foreach ($plotWidgetUrls as $plotWidgetUrl) { ?>
            <div class="embed-responsive embed-responsive-4by3">
                <iframe class="embed-responsive-item" src="<?php echo $plotWidgetUrl ?>" allowfullscreen></iframe>
            </div>
        <?php } ?>
        <?php
        if (isset($dataGrids)) {
            foreach ($dataGrids as $dataGrid) {
                \yii\widgets\Pjax::begin([
                    'enablePushState' => FALSE,
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataGrid["dataProvider"],
                    'columns' => $dataGrid["columnNames"]
                ]);
                \yii\widgets\Pjax::end();
            }
        }
    } else {
        ?>

        <?php
        if (Yii::$app->session->hasFlash("scriptDidNotWork")) {
            Html::tag('pre', Yii::$app->session->getFlash("scriptDidNotWork"));
        }
    }
    ?>

</div><!-- form -->