<?php

//******************************************************************************
//                                       view.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>, Arnaud Charleroy <arnaud.charleroy@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  22 august 2018 (add annotation functionnality)
// Subject: implements the view page for a vector
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\widgets\AnnotationWidget;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiVectorModel */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!--add annotation button-->
<?= AnnotationWidget::widget([AnnotationWidget::TARGETS => [$model->uri]]); ?>

<div class="vector-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
    <?php
        if (Yii::$app->session['isAdmin']) {
            echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']);
        }
    ?>
        <?= Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernUri' => $model->uri, 'concernLabel' => $model->label], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']); ?>
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