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
use yii\grid\GridView;
use app\controllers\VectorController;
use app\components\widgets\EventButtonWidget;
use app\components\widgets\EventGridViewWidget;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use yii\helpers\Url;
use app\models\yiiModels\YiiDocumentModel;

/** 
 * Implements the view page for a vector
 * @update [Arnaud Charleroy] 22 august, 2018: add annotation functionality
 * @update [Andréas Garcia] 06 March, 2019: add event button and widget 
 * @var $this yii\web\View
 * @var $model app\models\YiiVectorModel
 */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vector-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
    <?php
        if (Yii::$app->session['isAdmin']) {?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']); ?>
            <?= Html::a(Yii::t('app', 'Add Document'), [
                'document/create', 
                'concernedItemUri' => $model->uri, 
                'concernedItemLabel' => $model->label,
                YiiDocumentModel::RETURN_URL => Url::current()
            ], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']); ?>
            <?= EventButtonWidget::widget([EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri]]); ?>
            <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
        <?php
        }
    ?>
    </p>

<?= DetailView::widget([
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
            [
              'attribute' => 'personInCharge',
              'format' => 'raw',
              'value' => function ($model) {
                    return Html::a($model->personInCharge, ['user/view', 'id' => $model->personInCharge]);
                },
            ],
        ]
    ]); ?>
    
    <!-- Linked events -->
    <?= EventGridViewWidget::widget(
            [
                 EventGridViewWidget::EVENTS_PROVIDER => ${VectorController::EVENTS_PROVIDER}
            ]
        ); 
    ?>
    
    <!-- Vector linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                AnnotationGridViewWidget::ANNOTATIONS => ${VectorController::ANNOTATIONS_PROVIDER}
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