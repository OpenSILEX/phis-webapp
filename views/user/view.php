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
// Subject: implements the view page for a user
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiUserModel */

$this->title = $model->firstName . " " . $model->familyName ;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->email], ['class' => 'btn btn-primary']) ?>
        <?php //Html::a('Delete', ['delete', 'id' => $model->uri], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?php 
    $attributes;
    if (Yii::$app->session['isAdmin']) {
        $attributes = [
            'email',
            'firstName',
            'familyName',   
            'affiliation',
            'phone',
            'address',
            'orcid', 
            [
                'attribute' => 'isAdmin',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->isAdmin === "t" || $model->isAdmin === "true" || $model->isAdmin) {
                        return Yii::t('app', 'Yes');
                    } else {
                        return Yii::t('app', 'No');
                    }
                }
            ],
            [
                'attribute' => 'available',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->available === 0) {
                        return Yii::t('app', 'Unavailable');
                    } else {
                        return Yii::t('app', 'Available');
                    }
                }
            ],
            [
              'attribute' => 'groups',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->groups) > 0) {
                    foreach($model->groups as $group) {
                        $toReturn .= Html::a($group["name"], ['group/view', 'id' => $group["uri"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
        ];
    } else {
        $attributes = [
            'email',
            'firstName',
            'familyName',   
            'affiliation',
            'phone',
            'address',
            'orcid',
        ];
    }
    
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]); ?>

</div>
