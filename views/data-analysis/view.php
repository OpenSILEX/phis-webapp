<?php

use yii\helpers\Html;
use kartik\icons\Icon;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
echo Html::beginTag('div', ['class' => 'row']);
echo Html::tag('h5',
        Icon::show('flask', ['class' => 'fa-large'], Icon::FA). Yii::t('app/messages', 'Experimental version'),
        ['class' => ' alert alert-info col-sm-4 col-md-3']
        );
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'embed-responsive embed-responsive-4by3', 'style'=> 'overflow: hidden;margin:0;']);
echo Html::tag('iframe', "",['class' => 'embed-responsive-item', 'src' => $url,'allowfullscreen' => true]);
echo Html::endTag('div');
?>