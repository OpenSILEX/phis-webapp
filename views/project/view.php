<?php

//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  March, 2017
// Subject: implements the view page for a Project
//***********************************************************************************************

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\EventButtonWidget;
use app\components\widgets\EventGridViewWidget;
use app\controllers\ProjectController;
use app\models\yiiModels\YiiDocumentModel;

/** 
 * @update [Andréas Garcia] 06 March, 2019: add event button and widget 
 * @var $this yii\web\View
 * @var $model app\models\YiiProjectModel 
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (Yii::$app->session['isAdmin']) { ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']); ?>
            <?= Html::a(Yii::t('app', 'Add Document'), [
                'document/create', 
                'concernedItemUri' => $model->uri, 
                'concernedItemLabel' => $model->acronyme, 
                'concernedItemRdfType' => Yii::$app->params["Project"],
                YiiDocumentModel::RETURN_URL => Url::current()
            ], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning'])?>
            <?php //echo EventButtonWidget::widget([EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri]]); ?>
            <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]);?>
        <?php }
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uri',
            'acronyme',
            'name',
            //'parentProject',
            [
                'attribute' => 'parentProject',
                'format' => 'raw',
                'value' => Html::a($model->parentProject, ['view', 'id' => $model->parentProject])
            ],
            'subprojectType',
            'objective',
            'financialSupport',
            'financialName',
            [
                'attribute' => 'dateStart',
                'format' => 'raw',
                'value' => function($model) {
                    return date_format(date_create($model->dateStart), 'jS F Y');
                }
            ],
            [
                'attribute' => 'dateEnd',
                'format' => 'raw',
                'value' => function($model) {
                    return date_format(date_create($model->dateEnd), 'jS F Y');
                }
            ],
            [
                'attribute' => 'scientificContacts',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->scientificContacts) > 0) {
                        foreach ($model->scientificContacts as $scientificContact) {
                            $toReturn .= Html::a($scientificContact["firstName"] . " " . $scientificContact["familyName"], ['user/view', 'id' => $scientificContact["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'administrativeContacts',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->administrativeContacts) > 0) {
                        foreach ($model->administrativeContacts as $administrativeContact) {
                            $toReturn .= Html::a($administrativeContact["firstName"] . " " . $administrativeContact["familyName"], ['user/view', 'id' => $administrativeContact["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'projectCoordinatorContacts',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->projectCoordinatorContacts) > 0) {
                        foreach ($model->projectCoordinatorContacts as $projectCoordinatorContact) {
                            $toReturn .= Html::a($projectCoordinatorContact["firstName"] . " " . $projectCoordinatorContact["familyName"], ['user/view', 'id' => $projectCoordinatorContact["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'website',
                'format' => 'raw',
                'value' => Html::a($model->website, $model->website)
            ],
            'keywords',
            [
                'attribute' => 'description',
                'contentOptions' => ['class' => 'multi-line'], 
            ],
        ],
    ])
    ?>
    
    <!-- List of experiments -->
    <?= "<h3>" . Yii::t('app', 'Experiments') . "</h3>"; ?>
    <?= GridView::widget([
        'dataProvider' => ${ProjectController::EXPERIMENTS},
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'uri',
            'alias',
            'startDate',
            'endDate',
            'field',
            'campaign',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['experiment/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
    
    <!-- Sensor events -->
    <?php
//        echo EventGridViewWidget::widget(
//            [
//                 EventGridViewWidget::EVENTS => ${ProjectController::EVENTS}
//            ]
//        ); 
    ?>
    
    <!-- Project linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                 AnnotationGridViewWidget::ANNOTATIONS => ${ProjectController::ANNOTATIONS_DATA}
            ]
        ); 
    ?>
    
    <?php
    if ($dataDocumentsProvider->getCount() > 0) {
        echo "<h3>" . Yii::t('app', 'Linked Documents') . "</h3>";
        echo GridView::widget([
            'dataProvider' => $dataDocumentsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'title',
                'creator',
                [
                    'attribute' => 'creationDate',
                    'format' => 'raw',
                    'value' => function($model) {
                        return date_format(date_create($model->creationDate), 'jS F Y');
                    }
                ],
                'language',
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['document/view', 'id' => $model->uri]);
                        },
                    ]
                ],
            ]
        ]);
    }
    ?>

</div>
