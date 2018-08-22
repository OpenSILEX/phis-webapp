<?php
//**********************************************************************************************
//                                       index.php 
//
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

use kartik\icons\Icon;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AgronomicalObjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Agronomical Object} other{Agronomical Objects}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="agronomicalobject-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('yii', 'Create'), ['create-csv'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Icon::show('download-alt', [], Icon::BSG) . " " . Yii::t('yii', 'Download Search Result'), ['download-csv', 'model' => $searchModel], ['class' => 'btn btn-primary']) ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            [
                'attribute' => 'experiment',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    return Html::a($model->experiment, ['experiment/view', 'id' => $model->experiment]);
                },
                'filter' => \kartik\select2\Select2::widget([
                            'attribute' => 'experiment',
                            'model' => $searchModel,
                            'data' => $this->params['listExperiments'],
                            'options' => [
                                'placeholder' => 'Select experiment alias...'
                            ]
                        ]),
            ]
        ],
    ]); ?>
</div>