<?php

//**********************************************************************************************
//                                       create.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 4 2017
// Subject: creatin dataset by CSV
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiDatasetModel */
/* @var $handsontable openSILEX\handsontablePHP\adapter\HandsontableSimple */
/* @var $handsontableErrorsCellsSettings string */

$this->title = Yii::t('app', 'Add Data linked to a sensor');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dataset', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dataset-create_on_sensor">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form_on_sensor', [
        'model' => $model,
        'errors' => $errors,
        'handsontable' => isset($handsontable) ? $handsontable : null,
        'handsontableErrorsCellsSettings' =>  isset($handsontableErrorsCellsSettings) ? $handsontableErrorsCellsSettings : null
    ]) ?>
</div>
