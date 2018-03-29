<?php

//******************************************************************************
//                                       create.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: creation of sensors by csv
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = Yii::t('yii', 'Add Sensors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensor-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'sensorsTypes' => $sensorsTypes
    ]) ?>
</div>