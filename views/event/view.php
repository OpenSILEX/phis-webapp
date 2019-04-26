<?php
//******************************************************************************
//                                 view.php 
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: Feb. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ArrayDataProvider;
use app\components\helpers\Vocabulary;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\PropertyWidgetWithoutActions;
use app\components\widgets\ConcernedItemGridViewWidgetWithoutActions;
use app\controllers\EventController;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventAction;

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
        <?= Html::a(Yii::t('app', 'Update'), 
                ${EventController::PARAM_UPDATABLE} ? ['update', 'id' => $model->uri] : false, 
                array_merge(
                    [
                        'class' => 'btn btn-primary', 
                    ], 
                    ${EventController::PARAM_UPDATABLE} ? [] : [
                        'disabled' => 'disabled',
                        'title' => Yii::t('app', EventAction::EVENT_UNUPDATABLE_DUE_TO_UNUPDATABLE_PROPRTY_LABEL)
                        ])); ?>
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
    </p>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            YiiEventModel::URI,
            [
                'label' => Yii::t('app', YiiEventModel::TYPE_LABEL),
                'value' => Vocabulary::prettyUri($model->rdfType)
            ],
            YiiEventModel::DATE
        ],
    ])
    ?>
    
    <!-- Properties -->
    <?=
    PropertyWidgetWithoutActions::widget([
        YiiEventModel::PROPERTIES => $model->properties,
        'title' =>  Yii::t('app', 'Specific properties')
    ]);
    ?>
    
    <!-- Concerned items-->
    <?= ConcernedItemGridViewWidgetWithoutActions::widget(
        [
            ConcernedItemGridViewWidgetWithoutActions::DATA_PROVIDER => new ArrayDataProvider([
                'models' => $model->concernedItems,
                //SILEX:info
                //totalCount must be there too to get the pagination in GridView
                'totalCount' => count($model->concernedItems)
                //\SILEX:info
            ])
        ]); 
    ?>
    
    <!-- Linked Annotations-->
    <?= 
    AnnotationGridViewWidget::widget(
        [
             AnnotationGridViewWidget::ANNOTATIONS => ${EventController::PARAM_ANNOTATIONS_DATA_PROVIDER}
        ]
    ); 
    ?>
</div>
