<?php

//**********************************************************************************************
//                                       create.php 
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 08 2019
// Subject: creation of germplasm
//***********************************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiGermplasmModel */

$this->title = Yii::t('app', 'Add Germplasm');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="germplasm-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model,
        'germplasmTypes' => $germplasmTypes,
        'genusList' => $genusList,
        'speciesList' =>$speciesList,
        'varietiesList' =>$varietiesList,
        'accessionsList' =>$accessionsList,
        'lotTypesList' =>$lotTypesList,
        'selectedGermplasmType' => $selectedGermplasmType
    ]) ?>
</div>
