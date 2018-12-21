<?php

//******************************************************************************
//                                       index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 déc. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SpeciesSerarch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Species} other{Species}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'uri',
            'label'
        ],
    ]); ?>
</div>
