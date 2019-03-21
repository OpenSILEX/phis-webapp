<?php

//******************************************************************************
//                                 error.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;
use kartik\icons\Icon;

/* @var $this yii\web\View */

$this->title = Yii::t('app', '{n, plural, =1{Stat/Vizu Application} other{Stat/Vizu Applications}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
if(!isset($message)){
    $message = 'No application available.';
}


echo Html::tag("h3",
    Icon::show('window-close-o', ['class' => 'fab fa-large'], Icon::FA) . $message,
    ["class" => "alert alert-warning col-sm-6 col-md-5 col-md-offset-3 text-center"]
);
