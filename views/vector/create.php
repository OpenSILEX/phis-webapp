<?php

//******************************************************************************
//                                       create.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject: creation of vectors by csv (handsontable)
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiVectorModel */

$this->title = Yii::t('yii', 'Add Vectors');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensor-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'vectorsTypes' => $vectorsTypes,
        'users' => $users
    ]) ?>
</div>