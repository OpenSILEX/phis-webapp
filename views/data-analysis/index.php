<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\BaseHtml;
use kartik\icons\Icon;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\ScientificAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', 
        '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}',
        ['n' => 2]
        );
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag("p","Shiny server info : " . $shinyServerStatus,["class"  => "alert alert-warning"]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'display_name',
        'description',
        [
            'attribute' => 'documentCreationDate',
            'format' => 'text',
            'label' => 'creation Date',
        ],
        [
            'value' => 'application_url',
            'format' => 'raw',
              'value' => function ($model, $key, $index) {
                $openButton = Html::a(BaseHtml::icon('eye-open'),[
                    'data-analysis/view',
                    'url' => $model->application_url
                    ]);
                $externalLink = Html::a(Icon::show('external-link', [],
                        Icon::FA),
                        $model->application_url ,
                        ["target" => "_blank"]
                        );
                return $openButton . " " . $externalLink;
              },
        ],
    ],
]); 