<?php

//******************************************************************************
//                                 iframe-view.php
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

echo Html::tag('h3',
        Icon::show('flask', ['class' => 'fa-large'], Icon::FA). "Experimental version - Beta test ",
        ['class' => ' alert alert-warning']
        );

echo Html::beginTag('div', ['class' => 'embed-responsive embed-responsive-4by3', 'style'=> 'overflow: hidden']);
echo Html::tag('iframe', "",['class' => 'embed-responsive-item', 'src' => $appUrl,'allowfullscreen' => true]);
echo Html::endTag('div');
