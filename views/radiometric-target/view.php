<?php
//******************************************************************************
//                                  view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 01 Oct, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\event\EventButtonWidget;
use app\components\widgets\event\EventGridViewWidget;
use app\controllers\RadiometricTargetController;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\yiiModels\YiiDocumentModel;

/**
 * @var $this yii\web\View
 * @var $model app\models\YiiRadiometricTargetModel
 * @var $dataDocumentsProvider yii\data\DataProviderInterface
 * @var $radiometricTargetAnnotations yii\data\DataProviderInterface
 * @update [Andréas Garcia] 06 March, 2019: add event button and widget 
 */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="radiometric-target-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->session['isAdmin']) { ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']) ?>
            <?= EventButtonWidget::widget([EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri]]); ?>
            <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]) ?>
            <?= Html::a(Yii::t('app', 'Add Document'), [
                'document/create', 
                'concernedItemUri' => $model->uri, 
                'concernedItemLabel' => $model->label, 
                'concernedItemRdfType' => Yii::$app->params["RadiometricTarget"],
                YiiDocumentModel::RETURN_URL => Url::current()
            ], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']) ?>
        <?php } ?>
    </p>
    
    <!-- Radiometric target details -->
    <?php
    $attributes = [
            'label',
            'uri',
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
                'attribute' => 'material',
                'format' => 'raw',
                'value' => function ($model) {
                    $value = $model->material;
                    switch($model->material) {
                        case "carpet":
                            $value = Yii::t('app', 'Carpet');
                            break;
                        case "painting":
                            $value = Yii::t('app', 'Painting');
                            break;
                        case "spectralon":
                            $value = Yii::t('app', 'Spectralon');
                            break;
                        default:
                            break;
                    }
                    
                    return $value;
                },
            ],
            [
                'attribute' => 'shape',
                'format' => 'raw',
                'value' => function ($model) {
                    $value = $model->shape;
                    switch($model->shape) {
                        case "circular":
                            $value = Yii::t('app', 'Circular');
                            break;
                        case "rectangular":
                            $value = Yii::t('app', 'Rectangular');
                            break;
                        default:
                            break;
                    }
                    
                    return $value;
                },
            ],
        ];
                
    if ($model->shape == "circular") {
        $attributes[] = 'diameter';
    } else {
        $attributes[] = 'length';
        $attributes[] = 'width';
    }
    
    $attributes[] = 'brdfP1';
    $attributes[] = 'brdfP2';
    $attributes[] = 'brdfP3';
    $attributes[] = 'brdfP4';
                
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]); ?>
    
    <!-- Radiometric target events -->
    <?= EventGridViewWidget::widget(
            [
                 EventGridViewWidget::DATA_PROVIDER => ${RadiometricTargetController::EVENTS_DATA}
            ]
        ); 
    ?>
    
    <!-- Radiometric target linked Annotations -->
    <?=
    AnnotationGridViewWidget::widget(
            [
                AnnotationGridViewWidget::ANNOTATIONS => ${RadiometricTargetController::ANNOTATIONS_DATA}
            ]
    );
    ?>
    
    <!-- Radiometric target documents -->
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
</div>
