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
use miloschuman\highcharts\Highstock;
use yii\web\JsExpression;
use yii\helpers\Url;
use app\controllers\EventController;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\event\EventButtonWidget;

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
    <a role="button" data-toggle="collapse" href="#data-visualization-form" aria-expanded="true" aria-controls="data-visualization-form" style="font-size: 24px;"><i class ="glyphicon glyphicon-search"></i> <?= Yii::t('app', 'Search Criteria') ?></a>
    <div class="collapse in" id="data-visualization-form" >
        <?php
        
        $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => Url::to(['data-visualization', 'uri' => $model->uri, 'label' => $model->label, 'experimentUri' => $model->experiment]), //ensure you don't repeat get parameters
        ]);

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
                            <label class="control-label" ><?= Yii::t('app', 'Variable') ?>
                            </label>
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
                            // Create Provenance label select values array with the pattern {LABEL (prov:id)}
                            foreach ($this->params['provenances'] as $uri => $provenance) {
                                $provenancesArray[$uri] = $provenance->label . " (prov:" . explode("id/provenance/", $uri)[1] . ")";
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

                                <label class="control-label" ><?= Yii::t('app', 'Type') ?>
                                </label>
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
                                <label class="control-label" ><?= Yii::t('app', 'Camera position') ?>
                                </label>
                                <?php
                                echo \kartik\select2\Select2::widget([
                                    'name' => 'filter',
                                    'data' => Yii::$app->params['image.filter']['metadata.position'],
                                    'value' => $selectedPosition ? $selectedPosition : null,
                                    'options' => [
                                        'id' => 'filterSelect',
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
        <?php if (isset($data) && isset($show) && $show == true && !empty($data)) { ?>
            <div id="visualization-images" style='height:146px;'  >
                <div id='scientific-object-data-visualization-alert-div' >
                    <br>
                    <div class='alert alert-info' role='alert-info'>
                        <p>
                            <?php echo Yii::t('app/messages', 'You have to click a graphic point to see images on that date.'); ?>
                        </p>
                    </div>
                </div>
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
        <?php } ?>
        <div class="data-visualization-chart ">
            <?php
        }
        if (isset($data)) {
            if (empty($data)) {
                echo "  <div class='well '><p>" . Yii::t('app/messages', 'No result found.') . "</p></div>";
            } else {



                $url2 = Url::to(['image/search-from-scientific-object']);
                $objectURI = $model->uri;

                $series = [];
                foreach ($data as $dataFromProvenanceKey => $dataFromProvenanceValue) {

                    $series[] = [
                        'name' => $provenancesArray[$dataFromProvenanceKey],
                        'data' => $dataFromProvenanceValue["data"],
                        'id' => $dataFromProvenanceKey,
                        'visible' => true,
                    ];

                    if (!empty($dataFromProvenanceValue["photosSerie"])) {

                        foreach ($dataFromProvenanceValue["photosSerie"] as $photoKey => $photoValue) {
                            $info = "";
                            foreach ($photoValue as $photoValueEl) {
                                $info = $info . "<br>" . $photoValueEl[0] . "<br>" . $photoValueEl[1];
                            }
                            $photoSerie[] = [
                                'x' => $photoKey,
                                'title' => 'I',
                            ];
                        }
                        $series[] = [
                            'type' => 'flags',
                            'name' => 'images',
                            'data' => $photoSerie,
                            // 'allowOverlapX'=> true,
                            'onSeries' => $dataFromProvenanceKey,
                            'width' => 6,
                            'shape' => 'circlepin',
                            'lineWidth' => 1,
                            //  'y' => -15,
                            'events' => [
                                'click' => new JsExpression("
                                        function (event) { 
                                                           console.log(this);"
                                        . "                var searchFormData = new FormData();"
                                        . "                searchFormData.append('concernedItems[]', \"$objectURI\");"
                                        . "                searchFormData.append('serieIndex', this.index);"
                                        . "                searchFormData.append('pointIndex', event.point.index);"
                                        . "                searchFormData.append('DataFileSearch[rdfType]',\"$imageTypeSelected\");"
                                        . "                searchFormData.append('jsonValueFilter', \"$filterToSend\");"
                                        . "                searchFormData.append('startDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', event.point.x));"
                                        . "                searchFormData.append('endDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', event.point.x));"
                                        . "                searchFormData.append('imagesCount',$('#imagesCount').attr('data-id'));"
                                        . "                $.ajax({"
                                        . "                           url: \"$url2\","
                                        . "                          type: 'POST',"
                                        . "                   processData: false,"
                                        . "                      datatype: 'json',"
                                        . "                   contentType: false,"
                                        . "                          data: searchFormData,"
                                        . "                                                    }"
                                        . "                        ).done(function (data) {onDayImageListHTMLFragmentReception(data);}"
                                        . "                        ).fail(function (jqXHR, textStatus) {alert('ERROR : ' + jqXHR);});}"
                                        . "")
                            ]
                        ];
                    }
                }
                //var_dump($series);exit;
                foreach ($events as $event) {
                    $Eventsdata[] = [
                        'x' => $event['date'],
                        'title' => $event['title'],
                        'text' => $event['text']
                    ];
                }

                $eventsTab[] = [
                    'type' => 'flags',
                    'allowOverlapX' => true,
                    'name' => 'Events',
                    'lineWidth' => 1,
                    'y' => -40,
                    'data' => $Eventsdata,
                    'events' => [
                        'click' => new JsExpression("
                                        function (event) {
                                               console.log(this);
                                               console.log(event);
                                        }")
                    ]
                ];
                //var_dump($Eventsdata);exit;
                $eventCreateUrl = Url::to(['event/create',
                            EventController::PARAM_CONCERNED_ITEMS_URIS => [$objectURI],
                            EventController::PARAM_RETURN_URL => Url::current()]);

                $series[] = $eventsTab[0];
                $options = [
                    'id' => 'graphic',
                    'options' => [
                        'chart' => [
                            'zoomType' => 'x',
                            'type' => 'line'
                        ],
                        'title' => [
                            'text' => $this->title
                        ],
                        'subtitle' => [
                            'text' => Yii::t('app/messages', 'Click and drag in the plot area to zoom in!')
                        ],
                        'navigator' => [
                            'enabled' => true,
                            'margin' => 70,
                            'y' => -4
                        ],
                        'legend' => [
                            'enabled' => true,
                        ],
                        'xAxis' => [
                            'type' => 'datetime',
                            'title' => [
                                'text' => 'time'
                            ],
                            'ordinal' => false,
                            'crosshair' => true,
                        ],
                        'yAxis' => [
                            'title' => [
                                'text' => $this->title
                            ],
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
                                'dataGrouping' => [
                                    'enabled' => false
                                ],
                                //   'cursor' => 'pointer',
                                'marker' => [
                                    // 'enabled' => false,
                                    'states' => [
                                        'hover' => [
                                            'enabled' => true
                                        ],
                                        'radius' => 2
                                    ]
                                ],
                                'events' => [
                                    'click' => new JsExpression("
                                        function (event) {  
                                        
                                                 console.log(event);
                                                // var dates=this.data;
                                               //  var tab=[];
                                                 console.log('COMPARE');
                                                // dates.forEach(function(date) {tab.push(Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0200', date.x));});
                                                 //console.log(tab.sort());
                                                 var real=this.xAxis.toValue(event.chartX, false);
                                                 console.log(real);
                                                 console.log(event.point.x);
                                                 console.log(Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0200', real));
                                                 if(this.name!=='Events'){
                                                       var dateParams = '&dateWithoutTimezone='+Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S', event.point.x);
                                                       $('#createEventLink').attr('href',\"$eventCreateUrl\"+dateParams);
                                                       $('#events-lightbox').modal('show') ;}
                                                 
                                                 var time=Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0200', event.point.x);
                                                 console.log(time)}")
                                ]
                            ]
                        ]
                    ]
                ];

                echo Highstock::widget($options);
            }
        }
        ?>
        <div class="modal" id="events-lightbox">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h4 class="modal-title">Add annotation or event</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6 text-center">
                                <a class="btn btn-default" id="createEventLink">
                                    <span class="fa fa-flag fa-4x"></span>
                                    <p>Add Event</p>
                                </a>
                            </div>
                            <div class="col-md-6 text-center">
                                <a class="btn btn-default">
                                    <span class="fa fa-comment fa-4x"></span>
                                    <p>Add annotation</p>
                                </a>
                            </div>
                        </div>

                        <div class="row" id="add-event" >


                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


</div>
<script>
    $(document).ready(function () {

        $("#filterSelect").on("change", function (e) {
            var id = $("#filterSelect").select2("data")[0].id;
            console.log(id);

        });

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
    $(window).on('load', function () { // to put the script at the end of the page to 
    });

    /**
     * This function is call on the response of the ajax call when a user click on a point on the graphic to get images
     *  to be used with a carousel bootstrap widget and a vertical list up to the graphic.
     * @param String :Html content of views/image_simple_images_visualization.php 
     **/
    function onDayImageListHTMLFragmentReception(data) {

        var fragment = $(data);
        if ($.trim(fragment.find('#carousel-inner-fragment').html()) !== '') {
            $('#visualization-images-list').append(fragment.find('#image-visualization-list-fragment').html());
            $('#carousel-indicators').append(fragment.find('#carousel-indicators-fragment').html());
            $('#carousel-inner').append(fragment.find('#carousel-inner-fragment').html());
            $('[data-toggle="tooltip"]').tooltip();
            $('#imagesCount').attr('data-id', fragment.find('#counterFragment').attr('data-id'));
            $('#scientific-object-data-visualization-alert-div').hide();
        }
        var chart = $('#graphic').highcharts();
        $('#visualization-images-list li a img').each(function (index, value) {

            $(this).hover(function () {
                const point = $(this).attr('data-point');
                const serie = $(this).attr('data-serie');
                chart.series[serie].data[point].setState('hover');
                chart.tooltip.refresh(chart.series[serie].data[point]);
            }, function () {
                const point = $(this).attr('data-point');
                const serie = $(this).attr('data-serie');
                chart.series[serie].data[point].setState();
                chart.tooltip.hide();

            });

        });
    }

    var checked = $('#showWidget').is(':checked');
    if (checked) {
        $('#photoFilter').show();
    } else {
        // reset values
        $('#photoFilter').hide();
    }

    /**
     * Function apply when checkbox is clicked to show or not images.
     * @param String HTML The checkbox content
     **/
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

