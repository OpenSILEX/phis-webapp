<?php

//******************************************************************************
//                                 gallery.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', 
        '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}',
        ['n' => 2]
        );
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag("div", ["class" => "row"]);
echo \Yii::$app->view->renderFile($htmlDescriptionFilePath,[],'renderer');
echo Html::endTag("div");
