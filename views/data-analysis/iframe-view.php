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
echo Html::beginTag('div', ['class' => 'row']);
echo Html::tag('h5',
        Icon::show('flask', ['class' => 'fa-large'], Icon::FA). "Experimental version - Beta test ",
        ['class' => ' alert alert-info col-sm-4 col-md-3']
        );
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'embed-responsive embed-responsive-4by3', 'style'=> 'overflow: hidden']);
echo Html::tag('iframe', "",['class' => 'embed-responsive-item', 'src' => $appUrl,'allowfullscreen' => true]);
echo Html::endTag('div');
echo Html::endTag('div');
