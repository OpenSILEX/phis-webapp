<?php

//**********************************************************************************************
//                                       update.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June, 26 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 26 2017
// Subject: update document view
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */

$this->title = Yii::t('yii', 'Update') . ' ' . Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 1]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
