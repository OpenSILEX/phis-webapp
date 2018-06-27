<?php

//******************************************************************************
//                                       characterize.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 27 juin 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  27 juin 2018
// Subject:
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = Yii::t('yii', 'Characterize Sensors');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensor-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form-characterize', [
        'model' => $model,
        'sensorsTypes' => $sensorsTypes
    ]) ?>    
</div>