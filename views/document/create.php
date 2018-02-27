<?php

//**********************************************************************************************
//                                       create.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2017
// Subject: creation document view
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */

$this->title = Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 1]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 1]), 'url' => ['index']];

?>

<div class="document-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>