<?php

//**********************************************************************************************
//                                       create.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  February, 2017
// Subject: creation d'une expérimentation - vue
//***********************************************************************************************

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\YiiExperimentModel */

$this->title = Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 1]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>