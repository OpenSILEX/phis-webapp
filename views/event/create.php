<?php
//******************************************************************************
//                                  create.php 
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 6 March 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use yii\helpers\Html;

$this->title = Yii::t('yii', 'Register an event');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>