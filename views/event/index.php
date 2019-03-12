<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 02 jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider*/

$this->title = Yii::t('app', '{n, plural, =1{Event} other{Events}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rdfType',
                'format' => 'raw',
                'value' => function($model) {
                    $typeLabel = explode('#', $model->rdfType)[1];
                    return $typeLabel;
                },
                'filter' => false
            ],
            [
                'attribute' => 'concernedItemLabel',
                'format' => 'raw',
                'value' => function ($model) {
                    $concernedItemLabels = "";
                    $currentConcernedItemNumber = 0;
                    foreach ($model->concernedItems as $concernedItem) {
                        if ($currentConcernedItemNumber > 0) {
                            $concernedItemLabels .= "<br/>";
                        }

                        if ($currentConcernedItemNumber == Yii::$app->params['eventIndexNumberOfConcernedItemsToDisplay'])
                        {
                            $concernedItemLabels .= "...";
                            break;
                        }
                        else{
                            $concernedItemLabels .= implode(", ", $concernedItem->labels);
                        }
                        $currentConcernedItemNumber++;
                    }
                    return $concernedItemLabels;
                } 
            ],
            [
                'attribute' => 'dateRange', 
                'format' => 'raw',
                'value' => 'date',
                'filter' => DateRangePicker::widget([
                    'model'=> $searchModel,
                    'attribute' => 'dateRange',
                    'convertFormat'=>true,
                    'options' => array('class' => 'form-control date-range-input'),
                    'pluginOptions'=>[
                        'autoclose'=>true,
                        'timePicker'=>true,
                        'timePickerIncrement'=>15,
                        'locale'=>['format'=> Yii::$app->params['dateTimeFormatDateTimeRangePickerStandard']]
                    ]                            
                ])
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['event/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ]
    ]); ?>
</div>