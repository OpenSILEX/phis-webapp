<?php

//******************************************************************************
//                                       index.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 16 01 2020
// Contact: julien.bonnefont@inrae.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Images');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="data-images">
    <!-- 21:9 aspect ratio --><div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item"  src="<?= Html::encode($url) ?>" ></iframe> 
   </div>



</div>