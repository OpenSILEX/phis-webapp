<?php

//******************************************************************************
//                                 view.php 
// PHIS-SILEX
// Copyright © INRA 2017
// Creation date: Feb 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ArrayDataProvider;
use app\components\helpers\Vocabulary;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\PropertyWidget;
use app\components\widgets\ConcernedItemGridViewWidget;
use app\controllers\EventController;
use app\models\yiiModels\YiiEventModel;

/** 
 * @update [Andréas Garcia] 06 March, 2019: add event button and widget 
 * @var $this yii\web\View
 * @var $model app\models\YiiEventModel
 */

$this->title = $model->uri;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Event} other{Events}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">
    <h1><?= Html::encode(Vocabulary::prettyUri($model->rdfType)) ?></h1>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']); ?>
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            YiiEventModel::URI,
            [
                'label' => Yii::t('app', YiiEventModel::TYPE),
                'value' => Vocabulary::prettyUri($model->rdfType)
            ],
            YiiEventModel::DATE
        ],
    ])
    ?>
    
    <!-- Properties -->
    <?=
    PropertyWidget::widget([
        YiiEventModel::PROPERTIES => $model->properties,
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
    
    <!-- Linked Annotations-->
    <?= 
    AnnotationGridViewWidget::widget(
        [
             AnnotationGridViewWidget::ANNOTATIONS => ${EventController::ANNOTATIONS_DATA}
        ]
    ); 
    ?>

</div>
