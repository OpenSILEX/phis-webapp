<?php
//******************************************************************************
//                                   view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: Feb., 2017
// Contact: morgane.vidal@inra.fr, arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\EventButtonWidget;
use app\components\widgets\EventGridViewWidget;
use app\controllers\ExperimentController;
use app\components\widgets\LinkObjectsWidget;
use app\models\yiiModels\YiiDocumentModel;

/** 
 * Implements the view page for an Experiment
 * @update [Arnaud Charleroy] 23 august, 2018 (add annotation functionality)
 * @update [Andréas Garcia] 15 Jan., 2019: change "concern" occurences to "concernedItem"
 * @update [Andréas Garcia] 06 March, 2019: add event button and widget
 * @var $this yii\web\View
 * @var $model app\models\YiiExperimentModel 
 */

$this->title = $model->alias;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (Yii::$app->session['isAdmin'] || $this->params['canUpdate']) { ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']); ?>
            <?= Html::a(Yii::t('app', 'Add Document'), 
                    [
                        'document/create', 
                        'concernedItemUri' => $model->uri, 
                        'concernedItemLabel' => $model->alias, 
                        'concernedItemRdfType' => Yii::$app->params["Experiment"],
                        YiiDocumentModel::RETURN_URL => Url::current()
                    ], 
                    ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']) ?>            
            <?= EventButtonWidget::widget([EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri]]); ?>
            <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
            <?php
        }
        ?>
        
        <?= Html::a(Yii::t('app', 'Map Visualization'), 
                ['layer/view', 'objectURI' => $model->uri, 'objectType' => 'http://www.opensilex.org/vocabulary/oeso#Experiment', 'depth' => 'true', 'generateFile' => 'false'], ['class' => 'btn btn-info']) ?>
        </p>

    <?php
    $attributes;

    if (Yii::$app->session['isAdmin']) {
        $attributes = [
            'uri',
            'alias',
            [
                'attribute' => 'projects',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->projects) > 0) {
                        foreach ($model->projects as $project) {
                            $toReturn .= Html::a($project["acronyme"], ['project/view', 'id' => $project["uri"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'startDate',
                'format' => 'raw',
                'value' => function($model) {
                    return date_format(date_create($model->startDate), 'jS F Y');
                }
            ],
            [
                'attribute' => 'endDate',
                'format' => 'raw',
                'value' => function($model) {
                    return date_format(date_create($model->endDate), 'jS F Y');
                }
            ],
            'field',
            'campaign',
            'place',
            [
                'attribute' => 'scientificSupervisorContact',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->scientificSupervisorContacts) > 0) {
                        foreach ($model->scientificSupervisorContacts as $scientificSupervisor) {
                            $toReturn .= Html::a($scientificSupervisor["firstName"] . " " . $scientificSupervisor["familyName"], ['user/view', 'id' => $scientificSupervisor["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'technicalSupervisorContact',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->technicalSupervisorContacts) > 0) {
                        foreach ($model->technicalSupervisorContacts as $technicalSupervisorContact) {
                            $toReturn .= Html::a($technicalSupervisorContact["firstName"] . " " . $technicalSupervisorContact["familyName"], ['user/view', 'id' => $technicalSupervisorContact["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            'cropSpecies',
            'objective',
            //'groups',
            'keywords',
            [
                'attribute' => 'comment',
                'contentOptions' => ['class' => 'multi-line'], 
            ],                    
            [
                'attribute' => 'groups',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (is_array($model->groups) && count($model->groups) > 0) {
                        foreach ($model->groups as $group) {
                            $toReturn .= Html::a($group["name"], ['group/view', 'id' => $group["uri"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'variables',
                'format' => 'raw',
                'value' => function ($model) use ($variables) {
                    return LinkObjectsWidget::widget([
                        "uri" => $model->uri,
                        "updateLinksAjaxCallUrl" => Url::to(['experiment/update-variables']),
                        "items" => $variables,
                        "actualItems" => is_array($model->variables) ? array_keys($model->variables) : [],
                        "itemViewRoute" => "variable/view",
                        "conceptLabel" => "measured variables",
                        "canUpdate" => true,
                        "updateMessage" => Yii::t('app', 'Update measured variables'),
                        "infoMessage" => Yii::t('app/messages', 'When you change measured variables in the list, click on the check button to update them.')
                    ]);
                }
            ],
            [
                'attribute' => 'sensors',
                'format' => 'raw',
                'value' => function ($model) use ($sensors) {
                    return LinkObjectsWidget::widget([
                        "uri" => $model->uri,
                        "updateLinksAjaxCallUrl" => Url::to(['experiment/update-sensors']),
                        "items" => $sensors,
                        "actualItems" => is_array($model->sensors) ? array_keys($model->sensors) : [],
                        "itemViewRoute" => "sensor/view",
                        "conceptLabel" => "sensors",
                        "canUpdate" => true,
                        "updateMessage" => Yii::t('app', 'Update sensors'),
                        "infoMessage" => Yii::t('app/messages', 'When you change sensors in the list, click on the check button to update them.')
                    ]);
                }
            ]
        ];
    } else {
        $attributes = [
            'uri',
            'alias',
            [
                'attribute' => 'projects',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->projects) > 0) {
                        foreach ($model->projects as $project) {
                            $toReturn .= Html::a($project["acronyme"], ['project/view', 'id' => $project["uri"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            'startDate',
            'endDate',
            'field',
            'campaign',
            'place',
            [
                'attribute' => 'scientificSupervisorContacts',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->scientificSupervisorContacts) > 0) {
                        foreach ($model->scientificSupervisorContacts as $scientificSupervisor) {
                            $toReturn .= Html::a($scientificSupervisor["firstName"] . " " . $scientificSupervisor["familyName"], ['user/view', 'id' => $scientificSupervisor["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            [
                'attribute' => 'technicalSupervisorContacts',
                'format' => 'raw',
                'value' => function ($model) {
                    $toReturn = "";
                    if (count($model->technicalSupervisorContacts) > 0) {
                        foreach ($model->technicalSupervisorContacts as $technicalSupervisorContact) {
                            $toReturn .= Html::a($technicalSupervisorContact["firstName"] . " " . $technicalSupervisorContact["familyName"], ['user/view', 'id' => $technicalSupervisorContact["email"]]);
                            $toReturn .= ", ";
                        }
                        $toReturn = rtrim($toReturn, ", ");
                    }
                    return $toReturn;
                }
            ],
            'cropSpecies',
            'objective',
            //'groups',
            'keywords',
            'comment:ntext',
            [
                'attribute' => 'variables',
                'format' => 'raw',
                'value' => function ($model) use ($variables) {
                    return LinkObjectsWidget::widget([
                        "uri" => $model->uri,
                        "updateLinksAjaxCallUrl" => Url::to(['experiment/update-variables']),
                        "items" => $variables,
                        "actualItems" => is_array($model->variables) ? array_keys($model->variables) : [],
                        "itemViewRoute" => "variable/view",
                        "conceptLabel" => "measured variables",
                        "canUpdate" => false,
                        "updateMessage" => Yii::t('app', 'Update measured variables'),
                        "infoMessage" => Yii::t('app/messages', 'When you change measured variables in the list, click on the check button to update them.')
                    ]);
                }
            ],
            [
                'attribute' => 'sensors',
                'format' => 'raw',
                'value' => function ($model) use ($sensors) {
                    return LinkObjectsWidget::widget([
                        "uri" => $model->uri,
                        "updateLinksAjaxCallUrl" => Url::to(['experiment/update-sensors']),
                        "items" => $sensors,
                        "actualItems" => is_array($model->sensors) ? array_keys($model->sensors) : [],
                        "itemViewRoute" => "sensor/view",
                        "conceptLabel" => "sensors",
                        "canUpdate" => false,
                        "updateMessage" => Yii::t('app', 'Update sensors'),
                        "infoMessage" => Yii::t('app/messages', 'When you change sensors in the list, click on the check button to update them.')
                    ]);
                }
            ]
        ];
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]);
    ?>
    
    <?= EventGridViewWidget::widget(
            [
                 EventGridViewWidget::EVENTS => ${ExperimentController::EVENTS_DATA}
            ]
        ); 
    ?>
        
    <!-- Experiment linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                 AnnotationGridViewWidget::ANNOTATIONS => ${ExperimentController::ANNOTATIONS_DATA}
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
                'creationDate',
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

    <?php
    if ($dataAgronomicalObjectsProvider->getCount() > 0) {
        echo "<h3>" . Yii::t('app', 'Linked Scientific Objects') . "</h3>";
        echo GridView::widget([
            'dataProvider' => $dataAgronomicalObjectsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'uri',
                'alias',
                [
                    'attribute' => 'rdfType',
                    'format' => 'raw',
                    'value' => function($model, $key, $index) {
                        return explode("#", $model->rdfType)[1];
                    }
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
            ]
        ]);
    }
    ?>
</div>
