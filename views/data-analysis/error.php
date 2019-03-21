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
    $message = 'An error has occurred';
}


echo Html::tag("h4",
    Icon::show('window-close-o', ['class' => 'fab fa-large'], Icon::FA) . $message,
    ["class" => "alert alert-warning col-sm-5 col-md-4 col-md-offset-3 text-center"]
);
