<?php

//******************************************************************************
//                                       data_visualization.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 24 mai 2019
// Contact: julien.bonnefont@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use miloschuman\highcharts\Highstock;
use yii\web\JsExpression;
use yii\helpers\Url;
use app\controllers\EventController;
use app\models\yiiModels\YiiAnnotationModel;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\event\DetailEventGridViewWidget;

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
        <i class="fa fa-sliders"></i> <?= Yii::t('app', 'Visualization') ?>
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

                           
                            <?php
                            if (!empty(Yii::$app->params['image.filter'])) {
                                ?>
                                <div class="form-group col-md-12">

                                    <label class="control-label" ><?= Yii::t('app', 'Camera position') ?></label>

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

                            <?php } else {
                                ?>

                                <div class="form-group form-inline col-md-12">
                                    <label class="control-label" ><?= Yii::t('app', 'Metadata filter') ?></label>
                                    <?= Html::input('text', 'filterName',
                                            $selectedFilterName ? $selectedFilterName : '',
                                            $options = ['class' => 'form-control', 'placeholder' => Yii::t('app/messages', 'Position')]) ?>
                                    : <?= Html::input('text', 'filterValue',
                                            $selectedFilterValue ? $selectedFilterValue : '',
                                            $options = ['class' => 'form-control', 'placeholder' => Yii::t('app/messages', 'Top')]) ?>
                                </div>

                                <?php }
                            ?>
                        </div>
                    </fieldset>
                </div>
            </div>

            <?= Html::submitButton(Yii::t('app', 'Show'), ['class' => 'btn btn-primary ', 'id' => 'scientific-object-data-visualization-submit-button']) ?>

            <?php ActiveForm::end(); ?>
        </div>

        <?php if (isset($data) && isset($isPhotos) && $isPhotos && !empty($data)) { ?>
            <div id="visualization-images" style='height:246px;'  >
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

                // build Highcharts data series linked to photos series .
                // one for each provenances
                foreach ($data as $dataFromProvenanceKey => $dataFromProvenanceValue) {
                    $series[] = [
                        'name' => $provenancesArray[$dataFromProvenanceKey],
                        'data' => $dataFromProvenanceValue["data"],
                        'id' => $dataFromProvenanceKey, // id to link photo serie
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
                                            console.log(event.point.x);
                                            searchFormData.append('concernedItems[]', \"$objectURI\"); "
                                        . " searchFormData.append('serieIndex', this.index);"
                                        . " searchFormData.append('pointIndex', event.point.index);"
                                        . " searchFormData.append('DataFileSearch[rdfType]',\"$imageTypeSelected\");"
                                        . " searchFormData.append('jsonValueFilter', \"$filterToSend\");"
                                        . " searchFormData.append('provenance', \"$selectedProvenance\");"
                                        . " searchFormData.append('startDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', event.point.x));"
                                        . " searchFormData.append('endDate',Highcharts.dateFormat('%Y-%m-%dT%H:%M:%S+0000', event.point.x+1000));"
                                        . " searchFormData.append('imagesCount',$('#imagesCount').attr('data-id'));"
                                        . " $.ajax({"
                                        . "          url: \"$url2\","
                                        . "         type: 'POST',"
                                        . "  processData: false,"
                                        . "     datatype: 'json',"
                                        . "  contentType: false,"
                                        . "         data: searchFormData,"
                                        . "                                }).done(function (data) {"
                                       
                                        . "                                           console.log(data);onDayImageListHTMLFragmentReception(data);}"
                                        . "                                 ).fail(function (jqXHR, textStatus) {"
                                        . "                                           alert('ERROR : ' + jqXHR);});}")
                            ]
                        ];
                    }
                }

                // build Highcharts events flag serie
                foreach ($events as $event) {
                    $toReturn = '';
                    $marginLeft = 0;

                    foreach ($event['annotations'] as $annotation) {
                        $toReturn .= '<div class="well" style="margin:0px 0px 5px ' . $marginLeft . 'px;">';
                        $bodyValue = '';
                        foreach ($annotation['bodyValues'] as $i => $value) {
                            $splitSentence = $this->context->splitLongueSentence($value, 58);
                            $newSentence = '';
                            $size = sizeof($splitSentence);
                            foreach ($splitSentence as $j => $word) {
                                if ($j < $size - 1) {
                                    $newSentence .= '' . $word . '<br>';
                                } else {
                                    $newSentence .= '' . $word;
                                }
                            }
                            $bodyValue .= $newSentence;
                        }
                        $toReturn .= $bodyValue;
                        $marginLeft += 10;
                        $toReturn .= '<span class="pull-right">';
                        $toReturn .= date('d/m/Y H:i', strtotime($annotation['creationDate']));
                        $toReturn .= '</span></div>';
                    }
                    $Eventsdata[] = [
                        'x' => $event['date'],
                        'title' => $event['title'],
                        'text' => $toReturn,
                        'color' => $colorByEventCategorie[$event['title']]
                    ];
                }
                $eventsTab[] = [
                    'type' => 'flags',
                    'allowOverlapX' => true,
                    'name' => 'Events',
                    'lineWidth' => 1,
                    'y' => -40,
                    'clip' => false,
                    'data' => $Eventsdata,
                ];

                $series[] = $eventsTab[0];

                $eventCreateUrl = Url::to(['event/create',
                            EventController::PARAM_CONCERNED_ITEMS_URIS => [$objectURI],
                            EventController::PARAM_RETURN_URL => Url::current()]);

                $annotationCreateUrl = Url::to(['annotation/create',
                            YiiAnnotationModel::TARGETS => [$objectURI],
                            YiiAnnotationModel::RETURN_URL => Url::current()]);

                // the Highcharts json to build the graphic
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
                            'text' => Yii::t('app/messages', 'Click on a serie to add an event or annotate the scientific object.')
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
                            'useHTML' => true,
                            'xDateFormat' => '%Y-%m-%d %H:%M',
                            'formatter' => new JsExpression("function(tooltip) {
                                 if(this.points){
                                     return tooltip.defaultFormatter.call(this, tooltip);
                                 } else if(this.series.name=='Events'){
                                     const content = '<span>'+this.point.title+'</span><div>' + this.point.text + '</div>';
                                     return content;
                                 } else {
                                     return '';
                                 } 
                                            }")
                        ],
                        'plotOptions' => [
                            'line' => [
                                'marker' => [
                                    'enabled' => true,
                                    'symbol' => 'circle',
                                    'radius' => 3
                                ]
                            ],
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

                // INFO OF THE SCIENTIFIC OBJECT ( EVENTS TABLE + ANNOTATIONS TABLE )
                echo "<h3>" . Yii::t('app', 'Scientific object metadata') . "</h3>";
                Pjax::begin(['timeout' => 15000, 'id' => 'a']);
                echo DetailEventGridViewWidget::widget(
                        [
                            DetailEventGridViewWidget::DATA_PROVIDER => $eventsProvider,
                        ]
                );
                Pjax::end();

                Pjax::begin(['timeout' => 15000, 'id' => 'b']);
                echo AnnotationGridViewWidget::widget(
                        [
                            AnnotationGridViewWidget::ANNOTATIONS => $annotationsProvider
                ]);
                Pjax::end();
            }
        }
        ?>

        <!-- MODAL OPEN: CLICK ON THE DATA SERIES CURVE -->
        <div class="modal" id="add-event-annotation-lightbox" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge" aria-hidden="true">-->
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center">

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
        <!-- END OF MODAL  -->

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

    $(window).on('load', function () { // to put the script at the end of the page 
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

