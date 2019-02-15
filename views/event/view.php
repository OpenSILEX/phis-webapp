<?php

//**********************************************************************************************
//                                       view.php 
// PHIS-SILEX
// Copyright © INRA 2017
// Creation date: Feb 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\PropertiesWidget;
use app\components\widgets\ConcernedItemGridViewWidget;
use app\controllers\EventController;


/* @var $this yii\web\View */
/* @var $model app\models\YiiEventModel */

$this->title = $model->uri;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

    <h1><?= Html::encode($model->type) ?></h1>
    
    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']) ?>
        <!-- Add annotation button -->
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
        <?php
        if (Yii::$app->session['isAdmin']) {
         //   echo Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernedItemUri' => $model->uri, 'concernedItemLabel' => $model->acronyme, 'concernedItemRdfType' => Yii::$app->params["Project"]], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']);
        }
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uri',
            'type',
            'date'
        ],
    ])
    ?>
    
    <!-- Properties -->
    <?=
    PropertiesWidget::widget([
        'properties' => $model->properties,
        'title' =>  Yii::t('app', 'Specific properties')
    ]);
    ?>
    
    <!-- Concerned items-->
    <?= ConcernedItemGridViewWidget::widget(
            [
                 ConcernedItemGridViewWidget::CONCERNED_ITEMS => new ArrayDataProvider([
                    'models' => $model->concernedItems,
                    //SILEX:info
                    //totalCount must be there too to get the pagination in GridView
                    'totalCount' => count($model->concernedItems)
                    //\SILEX:info
                ])
            ]
        ); 
    ?>
    
    <!-- Linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                 AnnotationGridViewWidget::ANNOTATIONS => ${EventController::ANNOTATIONS_DATA}
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
