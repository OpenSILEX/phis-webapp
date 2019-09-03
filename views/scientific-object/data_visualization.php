<?php

//******************************************************************************
//                                       data_visualization.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 24 mai 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


/**
 * @var model app\models\yiiModels\YiiScientificObjectModel
 * @var variables array
 * @var data array
 * @var this yii\web\View
 */
?>

<div class="scientific-object-data-visualization">

    <a   role="button" data-toggle="collapse" href="#data-visualization-form" aria-expanded="true" aria-controls="data-visualization-form" style="font-size: 24px; line-height: 1.5em;"><i class ="fa-large fa fa-search"> <?= Yii::t('app', 'Search Criteria') ?></i></a>
    <div class="collapse in" id="data-visualization-form" >
        <?php
        $form = ActiveForm::begin();
        if (empty($variables)) {
            echo "<p>" . Yii::t('app/messages', 'No variables linked to the experiment of the scientific object.') . "</p>";
        } else {
            ?>
            <div class="form-row">

                <fieldset style="border: 1px solid #5A9016; padding: 10px;" >
                    <legend style="width: auto; border: 0; padding: 10px; margin: 0; font-size: 16px; text-align: center; font-style: italic" >
                        <?= Yii::t('app', 'Data Search') ?>
                    </legend>

                    <div class="form-row">
                        <div class="form-group required col-md-6">
                            <label class="control-label" ><?= Yii::t('app', 'Variable') ?></label>

                            <?php
                            echo \kartik\select2\Select2::widget([
                                'name' => 'variable',
                                'data' => $variables,
                                'value' => $selectedVariable ? $selectedVariable : null,
                                'options' => [
                                    'placeholder' => Yii::t('app/messages', 'Select a variable...'),
                                    'multiple' => false
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                'pluginEvents' => [
                                    "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text(\"" . Yii::t('app', 'Update') . "\"); }",
                                ]
                            ]);
                            ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label" ><?= Yii::t('app', 'Provenance') ?></label>

                            <?php
                            // Create Provenance select values array
                            foreach ($this->params['provenances'] as $uri => $provenance) {
                                $provenancesArray[$uri] = $provenance->label . " (" . $uri . ")";
                            }
                            ?>
                            <?php
                            echo \kartik\select2\Select2::widget([
                                'name' => 'provenances',
                                'data' => $provenancesArray,
                                'value' => $selectedProvenance ? $selectedProvenance : null,
                                'options' => [
                                    'placeholder' => Yii::t('app/messages', 'Select provenance...'),
                                    'multiple' => false
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                                'pluginEvents' => [
                                    "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text(\"" . Yii::t('app', 'Update') . "\"); }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <?=
                            \kartik\date\DatePicker::widget([
                                'name' => 'dateStart',
                                'options' => ['placeholder' => Yii::t('app/messages', 'Enter start date')],
                                'value' => isset($dateStart) ? $dateStart : null,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'orientation' => 'bottom'
                                ],
                                'pluginEvents' => [
                                    "changeDate" => "function() { $('#scientific-object-data-visualization-submit-button').text(\"" . Yii::t('app', 'Update') . "\");  }",
                                ]
                            ])
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <?=
                            \kartik\date\DatePicker::widget([
                                'name' => 'dateEnd',
                                'value' => isset($dateEnd) ? $dateEnd : null,
                                'options' => ['placeholder' => Yii::t('app/messages', 'Enter end date')],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'orientation' => 'bottom'
                                ],
                                'pluginEvents' => [
                                    "changeDate" => "function() { $('#scientific-object-data-visualization-submit-button').text(\"" . Yii::t('app', 'Update') . "\");  }",
                                ]
                            ])
                            ?>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="form-group" style="margin-bottom: 0px;">

                <?= Html::checkbox("show", isset($show) ? $show : false, ['id' => 'showWidget', 'label' => Yii::t('app', 'Show Images'), 'onchange' => 'onShow(this);']) ?>

            </div>

            <div class="form-row">
                <div id="photoFilter">
                    <fieldset style="border: 1px solid #5A9016; padding: 10px;" >
                        <legend style="width: auto; border: 0; padding: 10px; margin: 0; font-size: 16px; text-align: center; font-style: italic" >
                            <?= Yii::t('app', 'Image Search') ?>
                        </legend>

                        <div class="form-row">
                            <div class="form-group required col-md-12">

                                <label class="control-label" ><?= Yii::t('app', 'Type') ?></label>
                                <?php
                                echo \kartik\select2\Select2::widget([
                                    'name' => 'imageType',
                                    'data' => $this->params['imagesType'],
                                    'value' => $imageTypeSelected ? $imageTypeSelected : null,
                                    'options' => [
                                        'placeholder' => Yii::t('app/messages', 'Select image type...'),
                                        'multiple' => false
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    'pluginEvents' => [
                                        "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text(\"" . Yii::t('app', 'Update') . "\");  }",
                                    ],
                                ]);
                                ?>
                            </div>
                            <div class="form-group col-md-12">

                                <label class="control-label" ><?= Yii::t('app', 'Image View') ?></label>

                                <?php
                                echo \kartik\select2\Select2::widget([
                                    'name' => 'position',
                                    'data' => Yii::$app->params['image.filter']['metadata.position'],
                                    'value' => $selectedPosition ? $selectedPosition : null,
                                    'options' => [
                                        'placeholder' => Yii::t('app/messages', 'Select image view...'),
                                        'multiple' => false
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    'pluginEvents' => [
                                        "select2:select" => "function() {  $('#scientific-object-data-visualization-submit-button').text(\"" . Yii::t('app', 'Update') . "\"); }",
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <?= Html::submitButton(Yii::t('app', 'Show'), ['class' => 'btn btn-primary ', 'id' => 'scientific-object-data-visualization-submit-button']) ?>

            <?php ActiveForm::end(); ?>
        </div>
        <div id="visualization-images"   >

            <?php
            if (isset($data) && isset($show) && $show == true && !empty($data)) {
                echo "<div id='scientific-object-data-visualization-alert-div' style='height:146px;'><br><div class='alert alert-info' role='alert-info'>
                    <p>You have to click on data to see images</p></div></div>";
            }
            ?>
            <div id="imagesCount" style="display: none;" data-id=0 ></div>
            <ul id="visualization-images-list" class="images" >

            </ul>

            <div class="modal carousel and slide " data-ride="carousel"  data-interval="false" id="lightbox">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div    >
                                <ol id="carousel-indicators" class="carousel-indicators">

                                </ol>
                                <div id="carousel-inner" class="carousel-inner">

                                </div>
                                <!-- Controls -->
                                <a class="left carousel-control" href="#lightbox" role="button" data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="right carousel-control" href="#lightbox" role="button" data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>                   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="data-visualization-chart ">
            <?php
        }
        if (isset($data)) {
            if (empty($data)) {
                echo "  <div class='well '><p>" . Yii::t('app/messages', 'No result found.') . "</p></div>";
            } else {
                $series = [];
                foreach ($data["scientificObjectData"][0]["dataFromProvenance"]as $dataFromProvenanceKey => $dataFromProvenanceValue) {
                    $series[] = ['name' => $dataFromProvenanceKey,
                        'data' => $dataFromProvenanceValue];
                }
               // var_dump($series);
                $url2 = Url::to(['image/search-from-scientific-object']);
                $objectURI = $model->uri;
                if ($show) {
                    echo Highcharts::widget([
                        'id' => 'graphic',
                        'options' => [
                            'chart' => [
                                'zoomType' => 'x'
                            ],
                            'title' => [
                                'text' => $variables[$data["variable"]]
                            ],
                            'subtitle' => [
                                'text' => 'Click and drag in the plot area to zoom in'
                            ],
                            'xAxis' => [
                                'type' => 'datetime',
                                'title' => 'Date'],
                            'yAxis' => [
                                'title' => null,
                                'labels' => [
                                    'format' => '{value:.2f}'
                                ]
                            ],
                            'series' => $series,
                            'tooltip' => [
                                'xDateFormat' => '%Y-%m-%d %H:%M',
                            ],
                            'plotOptions' => [
                                'series' => [
                                    'cursor' => 'pointer',
                                    'point' => [
                                        'events' => [
                                            'click' => new JsExpression(" function() {"
                                                    . "var searchFormData = new FormData();"
                                                    . "console.log( Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0200', 1500768000000));"
                                                    . "searchFormData.append('concernedItems[]', \"$objectURI\");"
                                                    . "searchFormData.append('DataFileSearch[rdfType]',\"$imageTypeSelected\");"
                                                   // . "searchFormData.append('provenance',this.series.name);"
                                                    . "searchFormData.append('jsonValueFilter', \"$filterToSend\");"
                                                    . "searchFormData.append('startDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', this.x));"
                                                    . "searchFormData.append('endDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', this.x));"
                                                    . "searchFormData.append('imagesCount',$('#imagesCount').attr('data-id'));"
                                                    . "$.ajax({url: \"$url2\","
                                                    . "   type: 'POST',"
                                                    . "   processData: false,"
                                                    . "   datatype: 'json',"
                                                    . "   contentType: false,"
                                                    . "   data: searchFormData,"
                                                    . "}).done(function (data) {onDayImageListHTMLFragmentReception(data);}
                                                    ).fail(function (jqXHR, textStatus) {alert('ERROR : ' + jqXHR);});"
                                                    . "test2();}")
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]);
                } else {
                    echo Highcharts::widget([
                        'id' => 'graphic',
                        'options' => [
                            'time'=> ['timezoneOffset'=> -2 * 60],
                            'chart' => [
                                'zoomType' => 'x'
                            ],
                            'title' => ['text' => $variables[$data["variable"]]],
                            'subtitle' => [
                                'text' => 'Click and drag in the plot area to zoom in'
                            ],
                            'xAxis' => [
                                'type' => 'datetime',
                                'title' => 'Date',
                            ],
                            'yAxis' => [
                                'title' => null,
                                'labels' => [
                                    'format' => '{value:.2f}'
                                ]
                            ],
                            'series' => $series,
                            'tooltip' => [
                                'xDateFormat' => '%Y-%m-%d %H:%M',
                            ],
                            'plotOptions' => [
                                'series' => [
                                    'cursor' => 'pointer',
                                    'point' => [
                                        'events' => [
                                            'click' => new JsExpression(" function() {"
                                                    . "}")
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                }
            }
        }
        ?>
    </div>


</div>
<script> //Highcharts stuff
    $(document).ready(function () {

<?php
if (isset($data)) {
    echo "$('#data-visualization-form').collapse('hide');";
}
?>
        $('#data-visualization-form').on('hidden.bs.collapse', function () {
            $('#graphic').show();
            $('#visualization-images').show();
        });
        $('#data-visualization-form').on('show.bs.collapse', function () {
            $('#graphic').hide();
            $('#visualization-images').hide();
        });
    });
    /**
     in this html fragment, all images information for a day and one scientific object in a special HTML format to be used with a carousel bootstrap
     widget and a vertical list up to the graphic(..)
     
     **/
    function onDayImageListHTMLFragmentReception(data) {

        var fragment = $(data);
        if ($.trim(fragment.find('#carousel-inner-fragment').html()) !== '') {
            $('#scientific-object-data-visualization-alert-div').hide();
            $('#visualization-images-list').append(fragment.find('#image-visualization-list-fragment').html());
            $('#carousel-indicators').append(fragment.find('#carousel-indicators-fragment').html());
            $('#carousel-inner').append(fragment.find('#carousel-inner-fragment').html());
            $('[data-toggle="tooltip"]').tooltip();

            $('#imagesCount').attr('data-id', fragment.find('#counterFragment').attr('data-id'));
        }

    }


    function test2() {
        console.log("ça colle colle");

    }

    var checked = $('#showWidget').is(':checked');

    if (checked) {
        $('#photoFilter').show();
    } else {
        // reset values
        $('#photoFilter').hide();
    }

    function onShow(element) {
        $('#scientific-object-data-visualization-submit-button').text('<?php echo Yii::t('app', 'Update') ?>');
        var checked = $(element).is(':checked');
        if (checked) {
            $('#photoFilter').show();
        } else {
            // reset values
            $('#photoFilter').hide();
        }
    }

</script>

