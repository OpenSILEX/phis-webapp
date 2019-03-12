<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use nullref\datatable\DataTable;
use yii\bootstrap\Tabs;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app \app\models\yiiModels\DataAnalysisApp */

$date = new DateTime();
$this->title = Yii::t('app', $appConfiguration[$function]["label"] . " " . $date->format("Y-m-d") );

?>

<div class="form">
    <h2> <?= $appConfiguration[$function]["label"] ?> </h2>
    <br>
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(["data-analysis/run-script/", "function" => $function, 'rpackage' => $rpackage,]),
    ]);
    // construct form
    foreach ($model as $key => $attribute) {
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
                        $multiple = true;
                        $pluginOptions = [
                            'allowClear' => false,
                        ];
                        $option = [
                            'placeholder' => $parameters[$key]['label']
                        ];
                        if(isset($parameters[$key]['maxSelectedItem'])){
                            if($parameters[$key]['maxSelectedItem'] ==  1){
                                $option['multiple'] = false;
                            }else{
                                $pluginOptions["maximumSelectionLength"] = $parameters[$key]['maxSelectedItem'];
                                $option['multiple'] = true;
                            }
                        }
                        
                        echo $form->field($model, $key)->widget(Select2::classname(), [
                            'data' => $parametersValues[$key],
                            'size' => Select2::MEDIUM,
                            'options' => $option,
                            'pluginOptions' => $pluginOptions,
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
        // construct graph
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
        
        $tabItems = [];
        $active = true;
        $itemNumber = 1;
        // construct grid
        if (isset($dataGrids)) {
            foreach ($dataGrids as $dataGrid) {
                $ajaxColumns = [];
                    foreach ($dataGrid["columnNames"] as $value) {
                        $ajaxColumns[] = ["data" => $value];
                    }

                    $dt = DataTable::widget([
                                'dom' => 'Bfrtip',
                                'buttons' => [
                                    [
                                        'extend' => 'copyHtml5',
                                        'messageTop' => $exportGridParameters
                                    ],
                                    [
                                        'extend' => 'csvHtml5',
                                        'messageTop' => $exportGridParameters
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'messageTop' => $exportGridParameters
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'messageTop' => $exportGridParameters
                                    ],
                                ],
                                "ajax" => [
                                    "url" => Url::to(["data-analysis/ajax-session-get-data/",
                                        "sessionId" => $dataGrid["sessionId"],
                                        "dataId" => $dataGrid["dataId"]]),
                                    "dataSrc" => ""
                                ],
                                "columns" => [
                                    $ajaxColumns
                                ],
                                'columns' => $dataGrid["columnNames"],
                                'responsive' => true,
                                'autoWidth' => false
                    ]);

                    $tabItem = [
                        'label' => 'Dataset ' . $itemNumber,
                        'content' => $dt,
                    ];
                    // first grid
                    if ($active) {
                        $tabItem['active'] = $active;
                        $active = false;
                    }
                    $tabItems [] = $tabItem;

                    $itemNumber++;
                }
                echo Tabs::widget([
                    'items' => $tabItems
                ]);
            }            
    } else {
        if (Yii::$app->session->hasFlash("scriptDidNotWork")) {
            Html::tag('pre', Yii::$app->session->getFlash("scriptDidNotWork"));
        }
    }
    ?>
</div><!-- form -->