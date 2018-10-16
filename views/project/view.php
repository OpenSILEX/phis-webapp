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

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\controllers\ProjectController;


/* @var $this yii\web\View */
/* @var $model app\models\YiiProjectModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']) ?>
        <!-- Add annotation button -->
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
        <?php
        if (Yii::$app->session['isAdmin']) {
            echo Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernUri' => $model->uri, 'concernLabel' => $model->acronyme, 'concernRdfType' => Yii::$app->params["Project"]], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']);
        }
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
            'description',
        ],
    ])
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
