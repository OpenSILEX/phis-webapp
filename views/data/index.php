<?php

//******************************************************************************
//                                       index.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 22 mai 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $searchModel app\models\DataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $variables array */

$this->title = Yii::t('app', '{n, plural, =1{Data} other{Data}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="data-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    
   <?php 
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
              'attribute' => 'variable',
              'format' => 'raw',
              'value' => function ($model, $key, $index) {
                    return Html::a($model->variable->label, ['variable/view', 'uri' => $model->variable->uri]);
              },
              'filter' => \kartik\select2\Select2::widget([
                    'attribute' => 'variable',
                    'model' => $searchModel,
                    'data' => $variables,
                    'value' => $variables[0]
                ]),
            ],
            [
                'attribute' => 'date', 
                'format' => 'raw',
                'value' => 'date',
                'filter' => DateRangePicker::widget([
                    'model'=> $searchModel,
                    'attribute' => 'date',
                    'convertFormat'=>true,
                    'options' => array('class' => 'form-control date-range-input'),
                    'pluginOptions'=>[
                        'autoclose'=>true,
                        'timePicker'=>true,
                        'timePickerIncrement'=>15,
                        'locale'=>[
                            'format' => 'Y-m-dTH:i:sZZ'
                        ]
                    ]                            
                ])
            ],
            'value',
            [
              'attribute' => 'object',
              'format' => 'raw',
              'value' => function ($model) {
                    if ($model->object != null) {
                        $objectLabels = "";
                        foreach ($model->object->labels as $label) {
                            $objectLabels .= $label . "<br/> ";
                        }
                        return $objectLabels;
                    }
              }
            ],                    
            [
              'attribute' => 'provenance',
              'format' => 'raw',
              'value' => function ($model) {
                    if ($model->provenance != null) {
                        if ($model->provenance->label != null) {
                            return $model->provenance->label;
                        } else {
                            //SILEX:info
                            //it is a sensor. It is a temporary solution
                            //\SILEX:info
                            return Html::a($model->provenance->uri, ['sensor/view', 'id' => $model->provenance->uri]);
                        }
                    }
              }
            ],
        ],
    ]); ?>
</div>