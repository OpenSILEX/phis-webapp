<?php

//**********************************************************************************************
//                                       _form_dataset_graph.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October, 25 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 25 2017
// Subject: visualisation of dataset graph
//***********************************************************************************************

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
/* @var $data array */
/* @var $model app\models\YiiDatasetModel */

?>

<div class="dataset-visualisation well">
 <h3><?= Yii::t('app', 'Dataset(s) Visualization') ?> (<?= Yii::t('app', 'On selected plot(s)') ?>)</h3>
 
 
 <?php 
 if (isset($data["agronomicalObject"])) { 
          $series = [];
            foreach($data["agronomicalObject"] as $agronomicalObjectData) {
                $series[] = ['name' => $agronomicalObjectData["uri"],
                             'data' => $agronomicalObjectData["data"]];
            }
            
            echo Highcharts::widget([
                'id' => 'test',
                    'options' => [
                       'title' => ['text' => $this->params['variables'][$data["variable"]]],
                       'xAxis' => [
                          'type' => 'datetime',
                          'title' => 'Date',
                       ],
                       'yAxis' => [
                          'title' => null,
                           'labels' => [
                                'format' => '{value:.2f}'
                           ]
                       ],
                        'series' => $series,
                        
                    ]
                 ]);
        }
        ?>
         
 
    <div class="dataset-visualisation-form">
        <?php $form = ActiveForm::begin(); ?>
             <?=
             $form->field($model, 'variables')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['variables'],
                'options' => [
                    'placeholder' => 'Select a variable ...'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true
                ],
            ]); ?>

            <?= $form->field($model, 'dateStart')->widget(\kartik\date\DatePicker::className(), [
                'options' => [
                    'placeholder' => 'Enter date start'],            
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) ?>

            <?= $form->field($model, 'dateEnd')->widget(\kartik\date\DatePicker::className(), [
                'options' => [
                    'placeholder' => 'Enter date end'],            
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) ?>
            <div class="form-group">
                <?= Html::Button(Yii::t('yii', 'Search'), ['class' => 'btn btn-primary', 'id' => 'datasetSearchButton']) ?>
            </div>
         <?php ActiveForm::end(); ?>
         </div>
</div>
