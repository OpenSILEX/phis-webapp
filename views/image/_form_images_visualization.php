<?php

//******************************************************************************
//                                       _form_images_visualization.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: visualisation of images
//******************************************************************************
use kartik\sortable\Sortable;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $data array */
/* @var $model app\models\YiiImageModel */
?>

<div class="image-visualization">
    <h3><?= Yii::t('app', 'Images Visualization') ?> (<?= Yii::t('app', 'On selected plot') ?>)</h3>
    
    <div class="image-visualization-form">
        <?php $form = ActiveForm::begin(); ?>
             <?= $form->field($model, 'rdfType')->widget(\kartik\select2\Select2::classname(),[
                    'data' => $model->getRdfTypes(Yii::$app->session['access_token']),
                    'options' => [
                        'placeholder' => 'Select an image type if wanted ...'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            
            <div class="form-group">
                <?= Html::Button(Yii::t('yii', 'Show Images'), ['class' => 'btn btn-primary', 'id' => 'imageSearchButton']) ?>
            </div>
         <?php ActiveForm::end(); ?>
    </div>
    
    <div class="images">
        <?php 
            if (isset($data) && !empty($data->getModels())) {                
                //Preparation of the items array for the sortable widget
                $items = array();
                foreach ($data->getModels() as $image) {
                    $item['content'] = '<div class="grid-item image-definition">' . 
                                        Html::img($image->fileInformations->serverFilePath, 
                                                [
                                                    'width' => 200,
                                                    'onclick' => 'showImage("' . $image->fileInformations->serverFilePath . '")'
                                                ]) . 
                                        '<p>' . $image->configuration->date . '<br/>' . 
//                                        Yii::t('app', 'Sensor Position') . ': ' . $image->configuration->position . '</p>' . 
                                   '</div>';
                    $items[] = $item;
                }
                
                echo "<h4 style='text-align:center'>" . Yii::t('app', 'Images') . "</h4>";
                
                echo Sortable::widget([
                    'type'=>'grid',
                    'items' => $items,
                ]);
            } else {
                echo "<h4 style='text-align:center'>" . Yii::t('app', 'No result found') . "</h4>";
            }
        ?>
        <!--Image modal-->
        <div class="modal fade" id="modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <!--<h4 class="modal-title"></h4>-->
                    </div>
                    <div class="modal-body">
                        <img src=""alt="" id="modalImage" class="img-responsive">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script> //Modal show 
            function showImage(imagePath) {
                $('#modalImage').attr("src", imagePath);
                $('#modal').modal({show:true});
            }
        </script>
    </div>
</div>