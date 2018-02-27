<?php

//**********************************************************************************************
//                                       update.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: May 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  May, 2017
// Subject: update experiment view
//***********************************************************************************************


use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\YiiExperimentModel */

$this->title = Yii::t('yii', 'Update') . ' ' . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 1]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="experiment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

