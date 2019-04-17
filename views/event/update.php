<?php

//******************************************************************************
//                                update.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;

$this->title = Yii::t('yii', 'Update event');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Event} other{Events}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-update">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model,
        'hideFiles' => true
    ]) ?>
</div>