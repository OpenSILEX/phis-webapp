<?php

//******************************************************************************
//                                   view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 6 Apr, 2017
// Contact: morgane.vidal@inra.fr, arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\EventButtonWidget;
use app\components\widgets\EventGridViewWidget;
use app\components\widgets\LinkObjectsWidget;
use app\controllers\SensorController;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\yiiModels\YiiDocumentModel;

/**
 * Implements the view page for a sensor
 * @update [Arnaud Charleroy] 22 August, 2018: add annotation functionality
 * @update [Andréas Garcia] 06 March, 2019: add event button and widget 
 * @var $this yii\web\View
 * @var $model app\models\YiiSensorModel
 * @var $dataSearchModel app\models\yiiModels\DeviceDataSearch
 * @var $variables array
 */
$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$sensorProfilePropertiesCount = 0;
foreach ($model->properties as $property) {
    $propertyLabel = explode("#", $property->relation)[1];
    
    if ($propertyLabel !== "type" 
            && $propertyLabel !== "label" 
            && $propertyLabel !== "inServiceDate" 
            && $propertyLabel !== "personInCharge" 
            && $propertyLabel !== "hasSerialNumber" 
            && $propertyLabel !== "dateOfPurchase" 
            && $propertyLabel !== "dateOfLastCalibration" 
            && $propertyLabel !== "hasBrand" 
            && $propertyLabel !== "hasLens" 
            && $propertyLabel !== "measures"
            && $propertyLabel !== "hasModel"
    ) {
        $sensorProfilePropertiesCount++;
    }
}
?>

<div class="sensor-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (Yii::$app->session['isAdmin']) { ?>
            <?= Html::a(Yii::t('app', 'Characterize Sensor'), ['characterize', 'sensorUri' => $model->uri], ['class' => 'btn btn-success']); ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']); ?>
            <?= Html::a(Yii::t('app', 'Add Document'), [
                'document/create', 
                'concernedItemUri' => $model->uri, 
                'concernedItemLabel' => $model->label,
                YiiDocumentModel::RETURN_URL => Url::current()
            ], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']) ?>
            <?= EventButtonWidget::widget([EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri]]); ?>
            <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
            <?php
        }
        ?>
    </p>

    <?php if (Yii::$app->session['isAdmin']): ?>
    <?php endif; ?>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'label',
            'uri',
            [
                'attribute' => 'rdfType',
                'format' => 'raw',
                'value' => function ($model) {
                    return explode("#", $model->rdfType)[1];
                }
            ],
            'brand',
            'serialNumber',
            'inServiceDate',
            'model',
            'dateOfPurchase',
            'dateOfLastCalibration',
            [
                'attribute' => 'personInCharge',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->personInCharge, ['user/view', 'id' => $model->personInCharge]);
                },
            ],
            [
                'attribute' => 'variables',
                'format' => 'raw',
                'value' => function ($model) use ($variables) {
                    return LinkObjectsWidget::widget([
                        "uri" => $model->uri,
                        "updateLinksAjaxCallUrl" => Url::to(['sensor/update-variables']),
                        "items" => $variables,
                        "actualItems" => is_array($model->variables) ? array_keys($model->variables) : [],
                        "itemViewRoute" => "variable/view",
                        "conceptLabel" => "measured variables",
                        "updateMessage" => Yii::t('app', 'Update measured variables'),
                        "infoMessage" => Yii::t('app/messages', 'When you change measured variables in the list, click on the check button to update them.'),
                        "canUpdate" => Yii::$app->session['isAdmin'] ? true : false
                    ]);
                }
            ],
            [
                'attribute' => 'properties',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "<ul>";
                    foreach ($model->properties as $property) {
                        $propertyLabel = explode("#", $property->relation)[1];

                        if ($propertyLabel !== "type" 
                                && $propertyLabel !== "label" 
                                && $propertyLabel !== "inServiceDate" 
                                && $propertyLabel !== "personInCharge" 
                                && $propertyLabel !== "hasSerialNumber" 
                                && $propertyLabel !== "dateOfPurchase" 
                                && $propertyLabel !== "dateOfLastCalibration" 
                                && $propertyLabel !== "hasBrand" 
                                && $propertyLabel !== "hasLens" 
                                && $propertyLabel !== "measures"
                                && $propertyLabel !== "hasModel"
                        ) {
                            $toReturn .= "<li>"
                                    . "<b>" . explode("#", $property->relation)[1] . "</b>"
                                    . " : "
                                    . $property->value
                                    . "</li>";
                        } else if ($propertyLabel === "hasLens") {
                            $toReturn .= "<li>"
                                    . "<b>" . explode("#", $property->relation)[1] . "</b>"
                                    . " : "
                                    . Html::a($property->value, ['view', 'id' => $property->value])
                                    . "</li>";
                        }
                    }
                    $toReturn .= "</ul>";
                    return $toReturn;
                },
            ]
        ]
    ]); ?>

    <!-- Sensor data -->
    <?= $this->render('_form_sensor_graph', [
        'model' => $dataSearchModel,
        'variables' => $model->variables
    ]) ?>
    
    <!-- Sensor events -->
    <?= EventGridViewWidget::widget(
            [
                 EventGridViewWidget::EVENTS => ${SensorController::EVENTS_DATA}
            ]
        ); 
    ?>
    
    <!-- Sensor linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                AnnotationGridViewWidget::ANNOTATIONS => ${SensorController::ANNOTATIONS_DATA}
            ]
    );
    ?>

    <?php if ($dataDocumentsProvider->getCount() > 0) {
        echo "<h3>" . Yii::t('app', 'Linked Documents') . "</h3>";
        echo GridView::widget([
            'dataProvider' => $dataDocumentsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'title',
                'creator',
                'creationDate',
                'language',
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                                ['document/view', 'id' => $model->uri]); 
                        },
                    ]
                ],
            ]
        ]);
    }
    ?>
</div>
