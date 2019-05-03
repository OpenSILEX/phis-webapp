<?php

//******************************************************************************
//                                       view.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\event\EventButtonWidget;
use app\components\widgets\event\EventGridViewWidget;
use app\components\widgets\LinkObjectsWidget;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\yiiModels\YiiDocumentModel;

/**
 * Implements the view page for an actuator
 * @var $this yii\web\View
 * @var $model app\models\YiiActuatorModel
 * @var $dataSearchModel app\models\yiiModels\DeviceDataSearch
 * @var $variables array
 */
$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Actuator} other{Actuators}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="actuator-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (Yii::$app->session['isAdmin']) { ?>
            <?= Html::a(Yii::t('app', 'Add Document'), [
                'document/create', 
                'concernedItemUri' => $model->uri, 
                'concernedItemLabel' => $model->label,
                YiiDocumentModel::RETURN_URL => Url::current()
            ], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']) ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']); ?>
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
            'model',
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
                'value' => function ($model) use ($variables) {
                    return LinkObjectsWidget::widget([
                        "uri" => $model->uri,
                        "updateLinksAjaxCallUrl" => Url::to(['actuator/update-variables']),
                        "items" => $variables,
                        "actualItems" => is_array($model->variables) ? array_keys($model->variables) : [],
                        "itemViewRoute" => "variable/view",
                        "conceptLabel" => "measured variables",
                        "updateMessage" => Yii::t('app', 'Update measured variables'),
                        "infoMessage" => Yii::t('app/messages', 'When you change measured variables in the list, click on the check button to update them.'),
                        "canUpdate" => Yii::$app->session['isAdmin'] ? true : false
                    ]);
                }
            ]
        ]
    ]); ?>

    <!-- actuator data -->
    <?= $this->render('_form_actuator_graph', [
        'model' => $dataSearchModel,
        'variables' => $model->variables
    ]) ?>
    
    <!-- actuator events -->
    <?= EventGridViewWidget::widget(
            [
                 EventGridViewWidget::EVENTS_PROVIDER => ${app\controllers\ActuatorController::EVENTS_DATA}
            ]
        ); 
    ?>
    
    <!-- actuator linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                AnnotationGridViewWidget::ANNOTATIONS => ${app\controllers\ActuatorController::ANNOTATIONS_DATA}
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
