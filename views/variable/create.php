<?php
//**********************************************************************************************
//                                       create.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 27 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 27 2017
// Subject: création d'une variable
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelVariable app\models\YiiVariableModel */
/* @var $modelTrait app\models\YiiTraitModel */
/* @var $modelMethod app\models\YiiMethodModel */
/* @var $modelUnit app\models\YiiUnitModel */

$this->title = Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Variable} other{Variables}}', ['n' => 1]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Variable} other{Variables}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="variable-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'modelVariable' => $modelVariable,
        'modelTrait' => $modelTrait,
        'modelMethod' => $modelMethod,
        'modelUnit' => $modelUnit,
        'listTraits' => $listTraits,
        'listMethods' => $listMethods,
        'listUnits' => $listUnits
    ]) ?>