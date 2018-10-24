<?php
//******************************************************************************
//                           view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 6 Apr, 2017
// Contact: morgane.vidal@inra.fr, arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\controllers\SensorController;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */
/* Implements the view page for a sensor */
/* @update [Arnaud Charleroy] 22 august, 2018 (add annotation functionality) */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sensor-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <!-- Add annotation button-->
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
        <?= Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernUri' => $model->uri, 'concernLabel' => $model->label], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']) ?>
    </p>

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
                'value' => function ($model) {
                    $toReturn = "<ul>";
                    foreach ($model->variables as $variableUri => $variableLabel) {
                        $toReturn .= "<li>"
                        . Html::a($variableLabel, ['variable/view', 'uri' => $variableUri])
                        . "</li>";
                    }
                    $toReturn .= "</ul>";
                    return $toReturn;
                },
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
                                && $propertyLabel !== "serialNumber"
                                && $propertyLabel !== "dateOfPurchase"
                                && $propertyLabel !== "dateOfLastCalibration"
                                && $propertyLabel !== "hasBrand"
                                && $propertyLabel !== "hasLens"
                                && $propertyLabel !== "observes"
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
