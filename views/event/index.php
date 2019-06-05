<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 2 Jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use app\models\yiiModels\EventSearch;
use app\components\widgets\event\EventButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider*/

$this->title = Yii::t('app', '{n, plural, =1{' . EventSearch::EVENT_LABEL . '} ' . 'other{' . EventSearch::EVENTS_LABEL . '}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>      
    
    <?= EventButtonWidget::widget([EventButtonWidget::CONCERNED_ITEMS_URIS => []]); ?>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => EventSearch::SEARCH_TYPE,
                'format' => 'raw',
                'value' => function($model) {
                    $typeLabel = explode('#', $model->rdfType)[1];
                    return $typeLabel;
                },
                'filter' => false
            ],
            [
                'attribute' => EventSearch::CONCERNED_ITEMS,
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
                            $removeEmptyLabels = array_filter($concernedItem->labels, function($value) {
                                return $value != "";
                            });
                            if (count($removeEmptyLabels) == 0) {
                                $concernedItemLabels .= basename($concernedItem->uri);
                            } else {
                                $concernedItemLabels .= implode(", ", $removeEmptyLabels);
                            }
                        }
                        $currentConcernedItemNumber++;
                    }
                    return $concernedItemLabels;
                } 
            ],
            [
                'attribute' => EventSearch::SEARCH_DATE_RANGE, 
                'format' => 'raw',
                'value' => EventSearch::DATE,
                'filter' => DateRangePicker::widget([
                    'model'=> $searchModel,
                    'attribute' => EventSearch::SEARCH_DATE_RANGE,
                    'convertFormat'=>true,
                    'options' => array('class' => 'form-control date-range-input'),
                    'pluginOptions'=>[
                        'autoclose'=>true,
                        'timePicker'=>true,
                        'timePickerIncrement'=>15,
                        'locale'=>[
                            'format'=> Yii::$app->params['dateTimeFormatDateTimeRangePickerStandard']
                        ]
                    ]                            
                ])
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>', 
                                ['event/view', 'id' => $model->uri]
                        ); 
                    },
                ]
            ],
        ]
    ]); ?>
</div>