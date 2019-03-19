<?php
//******************************************************************************
//                                 form.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 20 mar 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use nullref\datatable\DataTable;
use yii\bootstrap\Tabs;
use yii\web\View;
use app\components\helpers\DynamicFormContentGenerator;

/* @var $this yii\web\View */
/* @var $model \app\models\yiiModels\DataAnalysisApp */

$date = new DateTime();
$this->title = Yii::t('app', $functionConfiguration["label"] . " " . $date->format("Y-m-d"));
?>

<div class="form">
    <h2> <?= $functionConfiguration["label"] ?> </h2>
    <br>
    <?php
    $formParameters = [
        'action' => Url::to([
            "data-analysis/run-script/",
            "function" => $function,
            'rpackage' => $rpackage
        ])
    ];
    $form = ActiveForm::begin($formParameters);
    DynamicFormContentGenerator::generateFormContent(
            $form, $model, $inputParameters, $valueParameters
    );
    ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php
    ActiveForm::end();
    ?>
    <?php
    // export searched paramaters
    if (isset($exportGridParameters)) {
        echo Html::tag('p', 'Search parameters', ["class" => "alert alert-warning"]);
        echo Html::tag('pre', $exportGridParameters);
    }
    // errors
    if (Yii::$app->session->hasFlash("scriptDidNotWork")) {
        echo Html::tag('p', 'Errors', ["class" => "alert alert-danger"]);
        echo Html::tag('pre', Yii::$app->session->getFlash("scriptDidNotWork"));
    } else {
        echo Html::tag('p', 'Results', ["class" => "alert alert-success"]);

        // construct graph(s)
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
        //               Plotly.d3.json("<?php // echo $plotConfigurationUrl   ?>", function(err, plotSchema) {
        //                    // assuming json is formatted as { "data": [/* */], "layout": {/* */} }
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
                    $gridSearchedParameters = 'Search parameters ' . $exportGridParameters;
                    $datatable = DataTable::widget([
                                'dom' => 'Bfrtip',
                                'buttons' => [
                                    [
                                        'extend' => 'copyHtml5',
                                        'messageTop' => $gridSearchedParameters
                                    ],
                                    [
                                        'extend' => 'csvHtml5',
                                        'messageTop' => $gridSearchedParameters
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'messageTop' => $gridSearchedParameters
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'messageTop' => $gridSearchedParameters
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
                        'label' => $dataGrid["dataId"] . ' Dataset',
                        'content' => $datatable,
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
        }
    }
    ?>
</div><!-- form -->