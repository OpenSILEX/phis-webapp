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

<div class="scientific-object-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php var_dump($test);
    ?>


    <?= Html::a(Yii::t('yii', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('yii', 'Update'), ['update'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Icon::show('download-alt', [], Icon::BSG) . " " . Yii::t('yii', 'Download Search Result'), ['download-csv', 'model' => $searchModel], ['class' => 'btn btn-primary']) ?>

    <a class="btn icon-btn btn-danger pull-right" href="#">
        <span class="glyphicon btn-glyphicon glyphicon-trash img-circle text-danger"></span>
        Clean
    </a>
    <button type="button" id="cart-btn" class="btn btn-warning pull-right" > 
        <span class="glyphicon glyphicon-shopping-cart ">
        </span>
        <strong id="cart-articles" >&nbsp;0
        </strong>
    </button>


    <?=
    GridView::widget([
        'id' => 'scientific-object-table',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($model) {
                    return ['value' => $model->uri];
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
                'attribute' => 'properties',
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
                'attribute' => 'experiment',
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
</div>
<script>
    console.log(<?php echo isset($page)? $page+1: 1 ?>);
    $('input:checkbox', $('#scientific-object-table')).change(function (e) {
        var checked = $(this).is(':checked');
        console.log("checked?: " + checked);
        console.log("value?: " + $(this).val());
        
        if (checked) {
            var ajaxUrl = '<?php echo Url::to(['scientific-object/add-to-cart']) ?>';
            $.post(ajaxUrl, {
                "page":'<?php echo isset($page)? $page+1: 1?>',
                "item": $(this).val()
            });
            console.log("checked");
        } else {
            var ajaxUrl = '<?php echo Url::to(['scientific-object/remove-to-cart']) ?>';
            $.post(ajaxUrl, {
                "item": $(this).val()
            });

        }


    });
    //To  remove a cookie from our domain / we have to remove the cookie when we first go to the page but how to know that ? how to know i go to another view/controller and be back ?
    //Cookies.remove('name', { path: '' }); 
    function alertCookie() {
        Cookies.set('name', 'lol');
        console.log(Cookies.get('name'));
    }
    alertCookie();

    // select all : ajaxcall to find all the sci object uri and keep it on the cookie



</script>