<?php

//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 27 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 27 2017
// Subject: index of variables (with search)
//***********************************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VariableSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Variable} other{Variables}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Variable} other{Variables}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
              'attribute' => 'trait',
              'format' => 'raw',
              'value' => function ($model,  $key, $index) {
                    return $model->trait->{'label'} ;
              },
                'filter' => \kartik\select2\Select2::widget([
                   'attribute' => 'trait',
                   'model' => $searchModel,
                   'data' => $listTraits,
                   'options' => [
                       'placeholder' => Yii::t('app', 'Select trait alias...')
                   ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                 ]),
            ],
            [
              'attribute' => 'method',
              'format' => 'raw',
              'value' => function ($model,  $key, $index) {
                    return $model->method->{'label'} ;
              },
              'filter' => \kartik\select2\Select2::widget([
                     'attribute' => 'method',
                     'model' => $searchModel,
                     'data' => $listMethods,
                     'options' => [
                         'placeholder' => Yii::t('app', 'Select method alias...')
                     ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                 ]),
            ],
            [
              'attribute' => 'unit',
              'format' => 'raw',
              'value' => function ($model,  $key, $index) {
                    return $model->unit->{'label'} ;
               },
                'filter' => \kartik\select2\Select2::widget([
                     'attribute' => 'unit',
                     'model' => $searchModel,
                     'data' => $listUnits,
                     'options' => [
                         'placeholder' => Yii::t('app', 'Select unit alias...')
                     ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                 ]),
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['variable/view', 'uri' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>

<?php 
//            [
//              'attribute' => 'trait',
//              'format' => 'raw',
//              'value' => function ($model,  $key, $index) {
//                    $toReturn = $model->trait->{'label'} ;
//                    if ($model->trait->ontologiesReferences !== null && count($model->trait->ontologiesReferences) > 0) {
//                        $toReturn .= "<br/><br/><b>" . Yii::t('app', 'Related References') . "</b>" . "<br/>";
//                        $instanceDefModel = new \app\models\yiiModels\YiiInstanceDefinitionModel();
//                        $relations = $instanceDefModel->getEntitiesPossibleRelationsToOthersConcepts();
//                        foreach ($model->trait->ontologiesReferences as $ontologyReference) {
//                            $object = "";
//                            if (strstr($ontologyReference->object, "#")) {
//                                $object = explode("#", $ontologyReference->object)[1];
//                            } else {
//                                $exp = explode("/", $ontologyReference->object);
//                                $object = $exp[count($exp) - 1];
//                            }
//
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
//                    return $toReturn;
//              }
//            ],
//            [
//              'attribute' => 'method',
//              'format' => 'raw',
//              'value' => function ($model,  $key, $index) {
//                    $toReturn = $model->method->{'label'} ;
//                    if ($model->method->ontologiesReferences !== null && count($model->method->ontologiesReferences) > 0) {
//                        $toReturn .= "<br/><br/><b>" . Yii::t('app', 'Related References') . "</b>" . "<br/>";
//                        $instanceDefModel = new \app\models\yiiModels\YiiInstanceDefinitionModel();
//                        $relations = $instanceDefModel->getEntitiesPossibleRelationsToOthersConcepts();
//                        foreach ($model->method->ontologiesReferences as $ontologyReference) {
//                            $object = "";
//                            if (strstr($ontologyReference->object, "#")) {
//                                $object = explode("#", $ontologyReference->object)[1];
//                            } else {
//                                $exp = explode("/", $ontologyReference->object);
//                                $object = $exp[count($exp) - 1];
//                            }
//
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
//                    return $toReturn;
//              }
//            ],
//            [
//              'attribute' => 'unit',
//              'format' => 'raw',
//              'value' => function ($model,  $key, $index) {
//                    $toReturn = $model->unit->{'label'} ;
//                    if ($model->unit->ontologiesReferences !== null && count($model->unit->ontologiesReferences) > 0) {
//                        $toReturn .= "<br/><br/><b>" . Yii::t('app', 'Related References') . "</b>" . "<br/>";
//                        $instanceDefModel = new \app\models\yiiModels\YiiInstanceDefinitionModel();
//                        $relations = $instanceDefModel->getEntitiesPossibleRelationsToOthersConcepts();
//                        foreach ($model->unit->ontologiesReferences as $ontologyReference) {
//                            $object = "";
//                            if (strstr($ontologyReference->object, "#")) {
//                                $object = explode("#", $ontologyReference->object)[1];
//                            } else {
//                                $exp = explode("/", $ontologyReference->object);
//                                $object = $exp[count($exp) - 1];
//                            }
//
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
//                    return $toReturn;
//              }
//            ], 
                    
//            [
//                'attribute' => 'ontologiesReferences',
//                'format' => 'raw',
//                'value' => function ($model) {
//                    $toReturn = "";
//                    $instanceDefModel = new \app\models\yiiModels\YiiInstanceDefinitionModel();
//                    $relations = $instanceDefModel->getEntitiesPossibleRelationsToOthersConcepts();
//                    if ($model->ontologiesReferences !== null && count($model->ontologiesReferences) > 0) {
//                        $toReturn .= "<b>" . Yii::t('app', 'Variable References') . " </b>" . "<br/>";
//                        foreach ($model->ontologiesReferences as $ontologyReference) {
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($ontologyReference->object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
//                    if ($model->trait->ontologiesReferences !== null && count($model->trait->ontologiesReferences) > 0) {
//                        $toReturn .= "<b>" . Yii::t('app', 'Trait References') . "</b>" . "<br/>";
//                        foreach ($model->trait->ontologiesReferences as $ontologyReference) {
//                            $object = "";
//                            if (strstr($ontologyReference->object, "#")) {
//                                $object = explode("#", $ontologyReference->object)[1];
//                            } else {
//                                $exp = explode("/", $ontologyReference->object);
//                                $object = $exp[count($exp) - 1];
//                            }
//
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
                    
//                    if ($model->method->ontologiesReferences !== null && count($model->method->ontologiesReferences) > 0) {
//                        $toReturn .= "<b>" . Yii::t('app', 'Method References') . "</b>" . "<br/>";
//                        foreach ($model->method->ontologiesReferences as $ontologyReference) {
//                            $object = "";
//                            if (strstr($ontologyReference->object, "#")) {
//                                $object = explode("#", $ontologyReference->object)[1];
//                            } else {
//                                $exp = explode("/", $ontologyReference->object);
//                                $object = $exp[count($exp) - 1];
//                            }
//
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
//                    
//                    if ($model->unit->ontologiesReferences !== null && count($model->unit->ontologiesReferences) > 0) {
//                        $toReturn .= "<b>" . Yii::t('app', 'Unit References') . "</b>" . "<br/>";
//                        foreach ($model->unit->ontologiesReferences as $ontologyReference) {
//                            $object = "";
//                            if (strstr($ontologyReference->object, "#")) {
//                                $object = explode("#", $ontologyReference->object)[1];
//                            } else {
//                                $exp = explode("/", $ontologyReference->object);
//                                $object = $exp[count($exp) - 1];
//                            }
//
//                            $toReturn .= "- " . $relations[$ontologyReference->property] 
//                                       . " " . Html::a($object, $ontologyReference->seeAlso) . "<br/>";
//                        }
//                    }
//                    return $toReturn;
//                }
//            ],
?>
