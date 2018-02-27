<?php
//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  February, 2017
// Subject: index of experiments (with search)
//***********************************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ExperimentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'uri',
            'alias',
            'startDate',
            'endDate',
//            [
//              'attribute' => 'projects',
//              'format' => 'raw',
//              'value' => function ($model,  $key, $index) {
//                $toReturn = "";
//                if (count($model->projects) > 0) {
//                    foreach($model->projects as $project) {
//                        $toReturn .= Html::a($project->{'acronyme'}, ['project/view', 'id' => $project->{'uri'}]);
//                        $toReturn .= ", ";
//                    }
//                    $toReturn = rtrim($toReturn, ", ");
//                }
//                return $toReturn;
//              }
//            ],
            'field',
            'campaign',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['experiment/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>
