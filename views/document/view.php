<?php

//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June, 19 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June 19, 2017
// Subject: implements the view page for a document
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\YiiDocumentModel */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-view">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
    <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->uri, 'concernedItems' => json_encode($this->params['listRealConcernedItems'], JSON_UNESCAPED_SLASHES)], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uri',
            'title',
            'creator',
            'documentType',   
            'creationDate',
            'language',
            [
                'attribute' => 'comment',
                'contentOptions' => ['class' => 'multi-line'], 
            ],     
            [
              'attribute' => 'concernedItems',
              'format' => 'raw',
              'value' => function ($model) {
                $toReturn = "";
                if (count($model->concernedItems) > 0) {
                    foreach($model->concernedItems as $concernedItem) {
                        $toReturn .= Html::a($concernedItem["uri"], [$concernedItem["type"] . '/view', 'id' => $concernedItem["uri"]]);
                        $toReturn .= ", ";
                    }
                    $toReturn = rtrim($toReturn, ", ");
                }
                return $toReturn;
              }
            ],
        ],
    ]) ?>
    
    <center>
        <?php
            $urlDownload = \config::path()['basePath'] . '/images/icons/view_64.png';
            echo Html::a('<img title="' . yii::t('app', 'View / Download') . '" alt="download", src="' . $urlDownload . '">', ['download', 'id' => $model->uri, 'format' => $model->format], []);
        ?>
        <p><?= yii::t('app', 'View / Download') ?></p>
    </center>
    
</div>
