<?php

//******************************************************************************
//                                       _form_data_graph.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
/* @var $data array */
/* @var $model app\models\YiiDatasetModel */

?>

<div class="dataset-visualisation well">
 <h3><?= Yii::t('app', 'Data Visualization') ?> (<?= Yii::t('app', 'On selected plot(s)') ?>)</h3>
 
 
 <?php 
 if (isset($data["agronomicalObjects"])) {
          $series = [];
            foreach($data["agronomicalObjects"] as $agronomicalObjectData) {
                if (array_key_exists("data", $agronomicalObjectData)) {
                    $series[] = ['name' => $agronomicalObjectData["uri"],
                            'data' => $agronomicalObjectData["data"]];
                }
            }
            if (count($series) == 0) {
                echo "<h4 style='text-align:center'>" . Yii::t('app', 'No result found') . "</h4>";
            } else {
                echo Highcharts::widget([
                        'id' => 'data-visualization',
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
                           'tooltip' => [
                               'xDateFormat'=> '%Y-%m-%dT%H:%M:%S',
                           ] 

                        ]
                     ]);
            }
        }
        ?>
         
 
    <div class="dataset-visualisation-form">
        <?php $form = ActiveForm::begin(); ?>
             <?=
             $form->field($model, 'variable')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['variables'],
                'options' => [
                    'placeholder' => 'Select a variable ...'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true
                ],
            ]); ?>

            <?= $form->field($model, 'startDate')->widget(\kartik\date\DatePicker::className(), [
                'options' => [
                    'placeholder' => 'Enter date start'],            
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) ?>

            <?= $form->field($model, 'endDate')->widget(\kartik\date\DatePicker::className(), [
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