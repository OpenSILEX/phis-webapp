<?php

//**********************************************************************************************
//                                       index.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: index of users
//***********************************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->session['isAdmin']) { ?>
        <?= Html::a(Yii::t('yii', 'Create') . ' '. Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
              'attribute' => 'email',
              'format' => 'raw',
               'value' => 'email',
              'filter' =>false,
            ],
            
            'firstName',
            'familyName',
            'affiliation',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['user/view', 'id' => $model->email]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>
