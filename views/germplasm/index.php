<?php

//**********************************************************************************************
//                                       index.php
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 25 2019
// Subject: index of germplasms
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Germplasm');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="germplasm-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
            if (Yii::$app->session['isAdmin']) {
                echo Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', 'Germplasm'), ['create'], ['class' => 'btn btn-success']) . "\t";
            }
        ?>
    </p>
     
</div>