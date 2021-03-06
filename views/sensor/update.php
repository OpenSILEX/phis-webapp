<?php

//******************************************************************************
//                                       update.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 26 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = Yii::t('yii', 'Update') . ' ' . Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 1]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="experiment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-update', [
        'model' => $model,
        'types' => $types,
        'users' => $users
    ]) ?>


