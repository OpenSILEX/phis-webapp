<?php

//******************************************************************************
//                                       view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiInfrastructureModel */

$this->title = $model->alias;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructure-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php 
            if (Yii::$app->session['isAdmin'] || $this->params['canUpdate']) {
                echo Html::a(Yii::t('app', 'Add Document'), ['document/create', 'concernedItem' => $model->uri], ['class' => $model->documents->getCount() > 0 ? 'btn btn-success' : 'btn btn-warning']);
            }
          ?>
    </p>
    
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'alias',
            'uri',
            'rdfType'
        ]
    ]); ?>
    
    <?php if ($model->documents->getCount() > 0) {
            echo json_encode($model->documents->getCount());
        
            echo "<h3>" . Yii::t('app', 'Linked Documents') . "</h3>";
            echo GridView::widget([
                'dataProvider' => $model->documents,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'title',
                    'creator',
                    'creationDate',
                    'language',
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                                ['document/view', 'id' => $model->uri]); 
                            },
                        ]
                    ],
                ]
            ]);
          } 
    ?>
</div>
