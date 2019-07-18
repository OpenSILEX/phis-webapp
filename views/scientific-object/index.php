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

    <p>
        <?= Html::a(Yii::t('yii', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('yii', 'Update'), ['update'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Icon::show('download-alt', [], Icon::BSG) . " " . Yii::t('yii', 'Download Search Result'), ['download-csv', 'model' => $searchModel], ['class' => 'btn btn-primary']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
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