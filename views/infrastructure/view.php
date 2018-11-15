<?php

//******************************************************************************
//                                       view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 28 Aug, 2018
// Contact: vincent.migot@inra.fr, morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\InfrastructureController;
use app\components\widgets\AnnotationGridViewWidget;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\PropertiesWidget;

/* @var $this yii\web\View */
/* @var $model app\models\YiiInfrastructureModel */
/* @var $dataDocumentsProvider yii\data\DataProviderInterface */
/* @update [Arnaud Charleroy] 28 August, 2018 : adding annotation linked to this infrastructure model */
/* @update [Vincent Migot] 20 Sept, 2018: implement view details from service
 */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Scientific frame} other{Scientific frames}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <!-- Add annotation button -->
        <?= AnnotationButtonWidget::widget([AnnotationButtonWidget::TARGETS => [$model->uri]]); ?>
        <?php
        if (Yii::$app->session['isAdmin']) {
            echo Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernUri' => $model->uri, 'concernLabel' => $model->label, 'concernRdfType' => Yii::$app->params["Installation"]], ['class' => $dataDocumentsProvider->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']);
        }
        ?>
    </p>
    <!-- Infrastructure properties detail-->
    <?=
    PropertiesWidget::widget([
        'uri' => $model->uri,
        'properties' => $model->properties,
        'aliasProperty' =>  Yii::$app->params["rdfsLabel"],
        'relationOrder' => [
            Yii::$app->params["rdfType"],
            Yii::$app->params["isPartOf"],
            Yii::$app->params["hasPart"],
        ]
    ]);
    ?>

    <!-- Infrastructure linked Annotation-->
    <?=
    AnnotationGridViewWidget::widget(
            [
                AnnotationGridViewWidget::ANNOTATIONS => ${InfrastructureController::ANNOTATIONS_DATA}
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
</div>
