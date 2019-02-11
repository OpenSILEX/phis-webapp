<?php

//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: implements the view page for a Group
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiGroupModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri], ['class' => 'btn btn-primary']) ?>
        <?php //Html::a('Delete', ['delete', 'id' => $model->uri], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uri',
            'name',
            'level',   
            [
                'attribute' => 'description',
                'contentOptions' => ['class' => 'multi-line'], 
            ],     
            [
              'attribute' => 'users',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->users) > 0) {
                    foreach($model->users as $user) {
                        $toReturn .= Html::a($user["firstName"] . " " . $user["familyName"], ['user/view', 'id' => $user["email"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
        ],
    ]) ?>

</div>