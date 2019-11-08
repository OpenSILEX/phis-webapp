<?php

//**********************************************************************************************
//                                       createCSV.php 
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2019
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 04 2019
// Subject: creation of germplasm
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiGermplasmModel */
/* @var $handsontable openSILEX\handsontablePHP\adapter\HandsontableSimple */
/* @var $handsontableErrorsCellsSettings string */

$this->title = Yii::t('app', 'Add Germplasm');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dataset', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="germplasm-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model,
        'errors' => $errors,
        'handsontable' => isset($handsontable) ? $handsontable : null,
        'handsontableErrorsCellsSettings' =>  isset($handsontableErrorsCellsSettings) ? $handsontableErrorsCellsSettings : null
       ]) ?>
</div>
