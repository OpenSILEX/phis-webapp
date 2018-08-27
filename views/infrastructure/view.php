<?php
//******************************************************************************
//                                       view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 Aug, 2018
// Contact: morgane.vidal@inra.fr,arnaud.charleroy, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\controllers\InfrastructureController;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\AnnotationButtonWidget;

/* @var $this yii\web\View */
/* @var $model app\models\YiiInfrastructureModel */
/* @update [Arnaud Charleroy] 28 August, 2018 : adding annotation 
 * linked to this infrastructure model*/

$this->title = $model->alias;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructure-view">

    <h1><?= Html::encode($this->title) ?></h1>
   
    <p>
        <!--add annotation button-->
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
        <?php 
            if (Yii::$app->session['isAdmin']) {
                echo Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernUri' => $model->uri, 'concernLabel' => $model->alias, 'concernRdfType' => Yii::$app->params["Installation"]], ['class' => $model->documents->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']);
            }
          ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'alias',
            'uri',
            'rdfType'
        ]
    ]); ?>
        <!-- AO Linked Annotation-->
    <?= AnnotationGridViewWidget::widget(
            [
                 AnnotationGridViewWidget::ANNOTATIONS => ${InfrastructureController::ANNOTATIONS_DATA}
            ]
        ); 
    ?>
        
    <?php 
        if ($model->documents->getCount() > 0) {
            echo json_encode($model->documents->getCount());
        
            echo "<h3>" . Yii::t('app', 'Linked Documents') . "</h3>";
            echo GridView::widget([
                'dataProvider' => $model->documents,
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
