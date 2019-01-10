<?php

//******************************************************************************
//                                       index.php
//
// Author(s): Andréas Garcia <andreas.garcia@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 02 janvier 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

$this->title = Yii::t('app', '{n, plural, =1{Event} other{Events}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn']
            , 'type'
            , [
                'attribute' => 'concernsLabel'
                , 'format' => 'raw'
                , 'value' => function ($model) {
                    $concernsString = "";
                    $first = true;
                    $concernsNumber = 1;
                    foreach ($model->concerns as $concernsItem) {
                            $first ? $first = false : $concernsString .= "<br>";
                            $concernsString .= implode(", ", $concernsItem->labels);
                            $concernsNumber++;
                            if ($concernsNumber >= Yii::$app->params['numberOfConcernsToDisplayInEventIndex'])
                            {
                                $concernsString .= "<br>...";
                                break;
                            }
                    }
                    return $concernsString;
                } 
            ]
            , [
                'attribute' => 'dateRange'
                , 'format' => 'raw'
                , 'value' => 'date'
                , 'filter' => DateRangePicker::widget([
                    'model'=> $searchModel
                    , 'attribute' => 'dateRange'
                    , 'convertFormat'=>true
                    , 'pluginOptions'=>[
                        'autoclose'=>true
                        , 'timePicker'=>true
                        , 'timePickerIncrement'=>15
                        , 'locale'=>['format'=> Yii::$app->params['standardDateTimeFormat']]
                    ]            
                ])
            ]
        ]
    ]); ?>
</div>