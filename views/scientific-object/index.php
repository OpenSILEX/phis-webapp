<?php

//**********************************************************************************************
//                                       index.php 
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

use kartik\icons\Icon;
use yii\helpers\Html;
use yii\helpers\Url;  
use yii\grid\GridView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\event\EventButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ScientificObjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="scientific-object-index">
    <h1><?= Html::encode($this->title) ?></h1>


    <?= Html::a(Yii::t('yii', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('yii', 'Update'), ['update'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Icon::show('download-alt', [], Icon::BSG) . " " . Yii::t('app', 'Export'), ['download-csv', 'model' => $searchModel], ['class' => 'btn btn-primary']) ?>

    <div class="btn-group pull-right">

        <button type="button" id="cart-btn" class="btn btn-warning">
            <span id="cart-span" class="glyphicon glyphicon-shopping-cart "></span>
            <strong id="cart-articles" data-count=" <?php echo $total; ?> " > <?php echo $total; ?></strong>
        </button>
        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><?=
                EventButtonWidget::widget([
                    EventButtonWidget::TYPE => "scientific-objects",
                    EventButtonWidget::CONCERNED_ITEMS_URIS => null,
                    EventButtonWidget::AS_LINK => false,
                ]);
                ?>
            </li>
            <li><?=
                Html::a(Icon::show('eraser', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('yii', 'Clear cart'), FALSE, ['onclick' => "$.ajax({
                                    type:'POST',
                                    url:'" . Url::to(['scientific-object/clean-cart']) . "',
                                    success:function(data) { afterClearCart(data);}});
                                   return false;",]
                );
                ?>
            </li>
        </ul>

    </div>
    <?=
    GridView::widget([
        'id' => 'scientific-object-table',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => " <div id='select-all-div'><input id='select-all-objects' type ='checkbox' value='{totalCount}' ><strong> " . Yii::t('app', 'Select all the') . " {totalCount} " . Yii::t('app', 'scientific objects') . " </strong></div>",
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($model) use($cart) {
                    $itemUri = $model->uri;
                    $itemName = $model->label;
                    $bool = isset($cart[$itemUri]);
                    return ['value' => $itemUri, 'data' => ['name' => $itemName],
                        'checked' => $bool];
                }],
            [
                'attribute' => 'uri',
                'format' => 'raw',
                'value' => 'uri'
            ],
            'label',
            [
                'attribute' => 'rdfType',
                'format' => 'raw',
                'value' => function($model, $key, $index) {
                    return explode("#", $model->rdfType)[1];
                },
                'filter' => \kartik\select2\Select2::widget([
                    'attribute' => 'type',
                    'model' => $searchModel,
                    'data' => $scientificObjectTypes,
                    'options' => [
                        'placeholder' => 'Select object type...'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]),
            ],
            [
                'attribute' => Yii::t('app', 'Properties'),
                'format' => 'raw',
                'value' => function($model, $key, $index) {
                    $toReturn = "<ul>";
                    foreach ($model->properties as $property) {
                        if (explode("#", $property->relation)[1] !== "type") {
                            $toReturn .= "<li>"
                                    . "<b>" . explode("#", $property->relation)[1] . "</b>"
                                    . " : "
                                    . $property->value
                                    . "</li>";
                        }
                    }
                    $toReturn .= "</ul>";
                    return $toReturn;
                },
            ],
            [
                'attribute' => Yii::t('app', 'Experiment'),
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    return Html::a($this->params['listExperiments'][$model->experiment], ['experiment/view', 'id' => $model->experiment]);
                },
                'filter' => \kartik\select2\Select2::widget([
                    'attribute' => 'experiment',
                    'model' => $searchModel,
                    'data' => $this->params['listExperiments'],
                    'options' => [
                        'placeholder' => 'Select experiment alias...'
                    ]
                ]),
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{event}<br/>{annotation}<br/>{dataVisualization}',
                'buttons' => [
                    'event' => function($url, $model, $key) {
                        return EventButtonWidget::widget([
                                    EventButtonWidget::TYPE => "one",
                                    EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri],
                                    EventButtonWidget::AS_LINK => true
                        ]);
                    },
                    'annotation' => function($url, $model, $key) {
                        return AnnotationButtonWidget::widget([
                                    AnnotationButtonWidget::TARGETS => [$model->uri],
                                    AnnotationButtonWidget::AS_LINK => true
                        ]);
                    },
                    'dataVisualization' => function($url, $model, $key) {
                        return Html::a(Icon::show('line-chart', ['class' => 'fa-large'], Icon::FA), ['data-visualization', 'uri' => $model->uri, 'label' => $model->label, 'experimentUri' => $model->experiment]);
                    },
                ]
            ]
        ],
    ]);
    ?>
    <!-- The modal -->
    <div class="modal  " id="cartView" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge" aria-hidden="true">
        <div class="vertical-alignment-helper">
            <div class="modal-dialog modal-lg vertical-align-center">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalLabelLarge"> <button id= "clean-cart-button" class="btn btn-danger btn-sm"><strong> <?= Icon::show('eraser', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('yii', 'Clear cart'); ?></strong></button>	</h4>
                    </div>

                    <div class="modal-body">
                        <table id="cart-table" class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th style="width:50%">uri</th>
                                    <th style="width:50%">label</th>
                                </tr>
                            </thead>
                            <tbody>    
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--/The modal -->

</div>
<script>

    //function who check the all object from the page case if all objects are checked
    function areAllItemsChecked() {
        var result = true;
        $('input:checkbox', $('#scientific-object-table .table')).each(function (index, value) {
            if (!$(this).is(':checked') && $(this).val() !== "1") {
                result = false;
                return false; // breaks
            }
        });
        return result;
    }

    //function to check is the all object from all page must be checked
    function areAllItemsFromAllPagesChecked() {
        return  +$('#cart-articles').text() === +$('#select-all-objects').val() ? true : false;
    }

    if (areAllItemsChecked()) {
        $('#scientific-object-table .select-on-check-all').prop("checked", true);
    }

    if (areAllItemsFromAllPagesChecked()) {
        $('#select-all-objects').prop("checked", true);
    }


    //function to get all objects on the page 
    function getAllObjectsWithNameFromThePage() {
        var items = {};
        $('input:checkbox', $('#scientific-object-table .table')).each(function (index, value) {
            if ($(this).val() !== "1") {
                items[$(this).val()] = $(this).attr("data-name");
            }
        });
        return items;
    }
    //Open the cart popup, get the cart and fill the popup
    $('#cart-btn').click(function () {

            var ajaxUrl = '<?php echo Url::to(['scientific-object/get-cart']) ?>';
            $.post(ajaxUrl).done(function (data) {
                var content = "";
                for (const [key, value] of Object.entries(data.items)) {
                    content += '<tr><td ><p>' + key + '</p></td><td ><p>' + value + '</p></td></tr>';
                }
                $('#cart-table tbody').html(content);
                $('#cartView').modal('show');
            }).fail(function (jqXHR, textStatus) {
                alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
            });

    });

    // show the eye-open in the car button on hover
    $('#cart-btn').mouseover(function () {
        $('#cart-span').removeClass().addClass('glyphicon glyphicon-eye-open');
        $('#cart-articles').hide();

    }).mouseout(function () {
        $('#cart-span').removeClass().addClass('glyphicon glyphicon-shopping-cart');
        $('#cart-articles').show();
    });

    // Add all the sci. obj. in the cart or remove them 
    $('#select-all-objects').change(function (e) {
        var checked = $(this).is(':checked');
        var uriSearchParameter = "<?php echo $searchParams["ScientificObjectSearch"]["uri"] ?>";
        var labelSearchParameter = "<?php echo $searchParams["ScientificObjectSearch"]["label"] ?>";
        var typeSearchParameter = "<?php echo $searchParams["ScientificObjectSearch"]["type"] ?>";
        var experimentSearchParameter = "<?php echo $searchParams["ScientificObjectSearch"]["experiment"] ?>";

        if (checked) {
            $('#cart-span').removeClass().addClass('glyphicon glyphicon-refresh glyphicon-refresh-animate');
            var ajaxUrl = '<?php echo Url::to(['scientific-object/all-to-add-to-cart']) ?>';
            $.post(ajaxUrl, {
                "uri": uriSearchParameter,
                "alias": labelSearchParameter,
                "type": typeSearchParameter,
                "experiment": experimentSearchParameter

            }).done(function (data) {
                $('input:checkbox', $('#scientific-object-table .table')).each(function (index, value) {
                    $(this).prop("checked", true);
                });
                $('#cart-articles').text(data.totalCount);
                $('#cart-span').removeClass().addClass('glyphicon glyphicon-shopping-cart');

            }).fail(function (jqXHR, textStatus) {
                alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
            });
        } else {
            $('input:checkbox', $('#scientific-object-table .table')).each(function (index, value) {
                $(this).prop("checked", false);
            });

            var ajaxUrl = '<?php echo Url::to(['scientific-object/all-to-remove-from-cart']) ?>';
            $.post(ajaxUrl).done(function (data) {
                $('#cart-articles').text(data.totalCount);
            }).fail(function (jqXHR, textStatus) {
                alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
            });
        }
    });

    // Add all the sci. obj. in the page or remove them
    $('#scientific-object-table .select-on-check-all').change(function (e) {
        var checked = $(this).is(':checked');
        if (checked) {

            var ajaxUrl = '<?php echo Url::to(['scientific-object/add-to-cart']) ?>';
            $.post(ajaxUrl, {
                "scientific-object": getAllObjectsWithNameFromThePage()
            }).done(function (data) {

                $('#cart-articles').text(data.totalCount);
                if (areAllItemsFromAllPagesChecked()) {
                    $('#select-all-objects').prop("checked", true);
                }
            }).fail(function (jqXHR, textStatus) {
                alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
            });

        } else {
            var ajaxUrl = '<?php echo Url::to(['scientific-object/remove-from-cart']) ?>';
            $.post(ajaxUrl, {
                "scientific-object": getAllObjectsWithNameFromThePage()
            }).done(function (data) {
                if ($('#select-all-objects').prop("checked")) {
                    $('#select-all-objects').prop("checked", false);
                }
                $('#cart-articles').text(data.totalCount);
            }).fail(function (jqXHR, textStatus) {
                alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
            });
        }
    });

    //Add one sci. obj. on the cart or remove him
    $('input:checkbox', $('#scientific-object-table .table')).change(function (e) {
        if ($(this).val() !== "1") { //checkbox value to select all the items from all pages
            var checked = $(this).is(':checked');
            if (checked) {
                var ajaxUrl = '<?php echo Url::to(['scientific-object/add-to-cart']) ?>';
                var itemsWithName = {};
                itemsWithName[$(this).val()] = $(this).attr("data-name");
                $.post(ajaxUrl, {
                    "scientific-object": itemsWithName
                }).done(function (data) {
                    $('#cart-articles').text(data.totalCount);
                    if (areAllItemsChecked()) {
                        $('#scientific-object-table .select-on-check-all').prop("checked", true);
                    }
                    if (areAllItemsFromAllPagesChecked()) {
                        $('#select-all-objects').prop("checked", true);
                    }
                }).fail(function (jqXHR, textStatus) {
                    alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
                });

            } else {
                var ajaxUrl = '<?php echo Url::to(['scientific-object/remove-from-cart']) ?>';
                var itemsWithName = {};
                itemsWithName[$(this).val()] = $(this).attr("data-name");
                $.post(ajaxUrl, {
                    "scientific-object": itemsWithName
                }).done(function (data) {
                    $('#cart-articles').text(data.totalCount);
                    if ($('#select-all-objects').prop("checked")) {
                        $('#select-all-objects').prop("checked", false);
                    }
                }).fail(function (jqXHR, textStatus) {
                    alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
                });
            }
        }
    });

    // Clean the cart
    $('#clean-cart-button').click(function () {
        var ajaxUrl = '<?php echo Url::to(['scientific-object/clean-cart']) ?>';
        $.post(ajaxUrl).done(function (data) {
            afterClearCart(data);
            $('#cart-table tbody').html("");
            $('#cartView').modal('hide');

        }).fail(function (jqXHR, textStatus) {
            alert('Something went wrong!/ERROR ajax callback : ' + jqXHR);
        });
    });

    /**
     * empty the popup, change the cart count and unchecked the checkbox
     * @param {json} callback
     * @returns {undefined}
     */
    function afterClearCart(data) {
        $('#cart-articles').text(data.totalCount);
        $('input:checkbox', $('#scientific-object-table .table')).each(function (index, value) {
            $(this).prop("checked", false);
        });
        $('#select-all-objects').prop("checked", false);
    }

</script>

