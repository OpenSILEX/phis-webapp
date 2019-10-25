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
use yii\grid\GridView;
use yii\widgets\Pjax;
use miloschuman\highcharts\Highstock;
use yii\web\JsExpression;
use yii\helpers\Url;
use app\controllers\EventController;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\event\EventButtonWidget;
use app\models\yiiModels\YiiAnnotationModel;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\event\EventGridViewWidget;

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
    <a role="button" data-toggle="collapse" href="#data-visualization-form" aria-expanded="true" aria-controls="data-visualization-form" style="font-size: 24px;">
        <i class="fa fa-line-chart"></i> <?= Yii::t('app', 'Visualization') ?>
    </a>
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
                        <?= Yii::t('app', 'Data search') ?>
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

        <?php if (isset($data) && isset($isPhotos) && $isPhotos && !empty($data)) { ?>
            <div id="visualization-images" style='height:146px;'  >
                <div id='scientific-object-data-visualization-alert-div' >
                    <br>
                    <div class='alert alert-info' role='alert-info'>
                        <p>
                            <?php echo Yii::t('app/messages', 'Click on the circle up to the serie to see images.'); ?>
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
                    $photoSerie = null;
                    if (!empty($dataFromProvenanceValue["photosSerie"])) {

                        foreach ($dataFromProvenanceValue["photosSerie"] as $photoKey => $photoValue) {
                            $photoSerie[] = [
                                'x' => $photoKey,
                                'title' => ' ',
                            ];
                        }
                        $imageSerieName = 'images/' . $provenancesArray[$dataFromProvenanceKey];
                        $series[] = [
                            'type' => 'flags',
                            'name' => $imageSerieName,
                            'data' => $photoSerie,
                            'onSeries' => $dataFromProvenanceKey,
                            'width' => 8,
                            'height' => 8,
                            'shape' => 'circlepin',
                            'lineWidth' => 1,
                            'point' => [
                                'events' => [
                                    'stickyTracking' => false,
                                    'mouseOver' => new JsExpression("
                                        function () {
                                            const pointIndex=this.index;
                                            const serieIndex=this.series.index;
                                            $('#visualization-images-list li a img').each(function (index, value) {
                                                  $(this).css('border-bottom', '');
                                                  const point = Number($(this).attr('data-point'));
                                                  const serie = Number($(this).attr('data-serie'));
                                                  if(pointIndex===point&&serieIndex===serie){
                                                       $(this).css('border', 'solid 2px orange');
                                                  }
                                            });
                                    }"),
                                    'mouseOut' => new JsExpression("function () {
                                                   $('#visualization-images-list li a img').each(function (index, value) {
                                                           $(this).css('border', ''); 
                                                           });
                                                                        }")
                                ]
                            ],
                            'events' => [
                                'click' => new JsExpression("
                                        function (event) { 
                                            var searchFormData = new FormData();
                                            searchFormData.append('concernedItems[]', \"$objectURI\"); "
                                        . " searchFormData.append('serieIndex', this.index);"
                                        . " searchFormData.append('pointIndex', event.point.index);"
                                        . " searchFormData.append('DataFileSearch[rdfType]',\"$imageTypeSelected\");"
                                        . " searchFormData.append('jsonValueFilter', \"$filterToSend\");"
                                        . " searchFormData.append('startDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', event.point.x));"
                                        . " searchFormData.append('endDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', event.point.x));"
                                        . " searchFormData.append('imagesCount',$('#imagesCount').attr('data-id'));"
                                        . " $.ajax({"
                                        . "          url: \"$url2\","
                                        . "         type: 'POST',"
                                        . "  processData: false,"
                                        . "     datatype: 'json',"
                                        . "  contentType: false,"
                                        . "         data: searchFormData,"
                                        . "                                }).done(function (data) {"
                                        . "                                          onDayImageListHTMLFragmentReception(data);}"
                                        . "                                 ).fail(function (jqXHR, textStatus) {"
                                        . "                                           alert('ERROR : ' + jqXHR);});}")
                            ]
                        ];
                    }
                }

                foreach ($events as $event) {
                    $Eventsdata[] = [
                        'x' => $event['date'],
                        'title' => $event['title'],
                        'text' => $event['id'],
                        'color' => $colorByEventCategorie[$event['title']]
                    ];
                }
                usort($Eventsdata, function ($item1, $item2) {
                    return $item1['x'] <=> $item2['x'];
                });

                $viewDetailUrl = Url::to(['event/ajax-view']);
                $eventsTab[] = [
                    'type' => 'flags',
                    'allowOverlapX' => true,
                    'name' => 'Events',
                    'lineWidth' => 1,
                    'y' => -40,
                    'clip' => false,
                    'data' => $Eventsdata,
                    'events' => [
                        'click' => new JsExpression("
                                        function (event) {
                                        const eventId=event.point.text;
                                        
                                         $.ajax({"
                                . "          url: \"$viewDetailUrl\","
                                . "         type: 'GET',"
                                . "     datatype: 'json',"
                                . "         data: { 
                                                             id: eventId},"
                                . "                                }).done(function (data) {"
                                . "                                            renderEventDetailModal(data);"
                                . "                                           console.log('ok');}"
                                . "                                 ).fail(function (jqXHR, textStatus) {"
                                . "                                           alert('ERROR : ' + jqXHR);});
                                        }")
                    ]
                ];
                $series[] = $eventsTab[0];

                $eventCreateUrl = Url::to(['event/create',
                            EventController::PARAM_CONCERNED_ITEMS_URIS => [$objectURI],
                            EventController::PARAM_RETURN_URL => Url::current()]);

                $annotationCreateUrl = Url::to(['annotation/create',
                            YiiAnnotationModel::TARGETS => [$objectURI],
                            YiiAnnotationModel::RETURN_URL => Url::current()]);


                $options = [
                    'id' => 'graphic',
                    'options' => [
                        'time' => ['timezoneOffset' => -2 * 60],
                        'chart' => [
                            'zoomType' => 'xy',
                            'type' => 'line',
                        ],
                        'title' => [
                            'text' => $variableInfo['label']
                        ],
                        'subtitle' => [
                            'text' => Yii::t('app/messages', 'Click on a serie to add an event!')
                        ],
                        'navigator' => [
                            'enabled' => true,
                            'margin' => 5,
                            'y' => -4
                        ],
                        'legend' => [
                            'layout' => 'vertical',
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
                                'text' => $variableInfo['label']
                            ],
                            'labels' => [
                                'format' => '{value:.2f}'
                            ]
                        ],
                        'series' => $series,
                        'tooltip' => [
                            'xDateFormat' => '%Y-%m-%d %H:%M',
                            'formatter' => new JsExpression("function(tooltip) {
                                 if(this.points){
                                     return tooltip.defaultFormatter.call(this, tooltip);
                                 } else if(this.series.name=='Events'){
                                     const content = '<br><span style=\"color:' + this.point.color + '\">' + this.point.title + '</span>';
                                     return content;
                                 } else {
                                     return '';
                                 } 
                                            }")
                        ],
                        'plotOptions' => [
                            'series' => [
                                'dataGrouping' => [
                                    'enabled' => false
                                ],
                                'marker' => [
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
                                                 var real=this.xAxis.toValue(event.chartX, false);
                                                 if(this.name!=='Events'){
                                                       var dateParams = '&dateWithoutTimezone='+Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000',real);
                                                       $('#createEventLink').attr('href',\"$eventCreateUrl\"+dateParams);
                                                       $('#createAnnotationLink').attr('href',\"$annotationCreateUrl\");
                                                       $('#add-event-annotation-lightbox').modal('show') ;
                                                 } 
                                                 }")
                                ]
                            ]
                        ]
                    ]
                ];

                echo Highstock::widget($options);

                Pjax::begin(['timeout' => 5000]);
                echo AnnotationGridViewWidget::widget(
                        [
                            AnnotationGridViewWidget::ANNOTATIONS => $annotationsProvider
                ]);
                Pjax::end();
                Pjax::begin(['timeout' => 5000]);
                echo EventGridViewWidget::widget(
                        [
                            EventGridViewWidget::DATA_PROVIDER => $eventsProvider,
                        ]
                );
                Pjax::end();
            }
        }
        ?>
        <div class="modal" id="add-event-annotation-lightbox" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog modal-lg vertical-align-center">

                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <a class="btn btn-default" id="createEventLink">
                                        <span class="fa fa-flag fa-4x"></span><br>
                                        <?= Yii::t('app', 'Add an event') ?>
                                    </a>
                                </div>
                                <div class="col-md-6 text-center">
                                    <a class="btn btn-default" id="createAnnotationLink">
                                        <span class="fa fa-comment fa-4x"></span><br>
                                        <?= Yii::t('app', 'Add an annotation on the object ') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="show-event-lightbox" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog modal-lg vertical-align-center">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><?= Yii::t('app', 'Description') ?></h4>
                        </div>
                        <div class="modal-body">
                            <div  class="table-responsive">
                                <div class=container-fluid>

                                </div>

                            </div>
                        </div>
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
        });



<?php
if (isset($data)) {
    echo "$('#data-visualization-form').collapse('hide');";
}
?>
        $('#data-visualization-form').on('hidden.bs.collapse', function () {
            $('#graphic').show();
            $('#visualization-images').show();
            $('#all-events-view').show();
        });
        $('#data-visualization-form').on('show.bs.collapse', function () {
            $('#graphic').hide();
            $('#visualization-images').hide();
            $('#all-events-view').hide();
        });

    });
    $(window).on('load', function () { // to put the script at the end of the page to 

    });

    /**
     * This function is call on the response of the ajax call when a user click on a flag associated to a serie to get images
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

    /**
     * This function is call on the response of the ajax call when a user click on a point on the graphic to get images
     *  to be used with a carousel bootstrap widget and a vertical list up to the graphic.
     * @param String :Html content of event/view.php 
     **/
    function renderEventDetailModal(data) {

        var fragment = $(data);
        $('#show-event-lightbox .modal-body .table-responsive .container-fluid').html(fragment);
        $('#show-event-lightbox').modal();
        $('#show-event-lightbox').modal('show');
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

