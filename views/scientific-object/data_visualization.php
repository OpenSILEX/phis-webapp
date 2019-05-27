<?php

//******************************************************************************
//                                       data_visualization.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 24 mai 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var model app\models\yiiModels\YiiScientificObjectModel
 * @var variables array
 * @var data array
 * @var this yii\web\View
 */

?>
<div class="scientific-object-data-visualization">
    <div class="data-visualization-form well">
        <?php $form = ActiveForm::begin(); 
        if (empty($variables)) {
            echo "<p>" . Yii::t('app/messages', 'No variables linked to the experiment of the scientific object.') . "</p>";
        } else {
            echo \kartik\select2\Select2::widget([
                'name' => 'variable',
                'data' => $variables,
                'options' => [
                    'placeholder' => Yii::t('app/messages','Select a variable ...'),
                    'multiple' => false
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
            <br/>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Show data'), ['class' => 'btn btn-primary']) ?>
            </div>
<?php   }
        if (isset($data)) {
            if (empty($data)) {
                echo "<p>" . Yii::t('app/messages', 'No result found.') . "</p>";
            } else {                
                $series = [];
                $series[] = ['name' => $data["scientificObjectData"][0]["label"],
                             'data' => $data["scientificObjectData"][0]["data"]];
                
            
            echo Highcharts::widget([
                'id' => 'test',
                    'options' => [
                        'title' => ['text' => $data["variable"]],
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

    <?php ActiveForm::end(); ?>
    </div>
</div>