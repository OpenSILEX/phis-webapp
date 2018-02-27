<?php

//**********************************************************************************************
//                                       _formCreateCSV.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 31 2017
// Subject: creation of agronomical objects via CSV
//***********************************************************************************************

//use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\YiiAgronomicalObjectModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="agronomicalobject-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <?= Yii::$app->session->getFlash('created'); ?>
    <?= Yii::$app->session->getFlash('error'); ?>

    <div class="alert alert-info" role="alert">
        <b><?= Yii::t('app/messages', 'File Rules')?> : </b>
        <table class="table table-hover">
            <tr>
                <th>Alias</th>
                <td><?= Yii::t('app/messages', 'The alias of the plot (e.g. MAU17-PG_38_WW_1_19_5)')?></td>
            </tr>
            <tr>
                <th style="color:red">Geometry *</th>
                <td><p><b style="color:red">WGS84 (EPSG:4326)</b> - <?= Yii::t('app/message', 'e.g. of coordinates') ?> : 3.974014429 43.61252934</p> <p><?= Yii::t('app/messages', 'Expected format') ?> : POLYGON (( 1.33 2.33, 3.44 5.66, 4.55 5.66, 6.77 7.88, 1.33 2.33))</p></td>
            </tr>
             <tr>
                <th style="color:red">ExperimentURI *</th>
                <td><?= Yii::t('app/messages', 'The URI of the experiment (e.g. http://www.phenome-fppn.fr/pheno3c/P3C2017-6)') ?></td>
            </tr>
            <tr>
                <th>Species</th>
                <td><p><?= Yii::t('app/messages', 'The URI of the species (e.g. http://www.phenome-fppn.fr/id/species/zeamays)') ?> </p>
                    <p><?= Yii::t('app/messages', 'See the folowing list to get all species URI') ?> :</p>
                    <ul class="ul_tiret">
                 <?php 
                    $speciesURIs = $model->getSpeciesUriList();
                    foreach ($speciesURIs as $species) {
                        echo "<li class=\"li_tiret\">" . $species . "</li>";
                    }
                ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Variety') ?></th>
                <td><?= Yii::t('app/messages', 'The variety used in the plot (e.g. apache)') ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Experiment Modalities') ?></th>
                <td><?= Yii::t('app/messages', 'The experiment modalities of the plot (e.g. WW, WD)') ?></td>
            </tr>
            <tr>
                <th><?= Yii::t('app', 'Replication') ?></th>
                <td><?= Yii::t('app/messages', 'The replication of the plot (e.g. 2, A)') ?></td>
            </tr>
        </table>
    </div>
    
    <p><i>
        <?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Template'), \config::path()['basePath'] . '/documents/AOFiles/gabaritAO.csv') ?>     
    </i></p>
    
    <?php echo $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => [
            'maxFileSize' => 2000,
            'pluginOptions'=>['allowedFileExtensions'=>['csv'],'showUpload' => false],
        ]
    ]); 
    ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii' , 'Create') , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>