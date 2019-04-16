<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use app\models\yiiModels\DataAnalysisAppSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', '{n, plural, =1{Stat/Vizu Application} other{Stat/Vizu Applications}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->session->hasFlash('scriptNotAvailable')) {
    echo Html::tag("p", "Script not available", ["class" => "alert alert-danger"]);
}

echo Html::beginTag("div", ["class" => "data-analysis-index"]);
echo Html::beginTag("div", ["class" => "row"]);
// each thumbnail (R application vignette)
foreach ($dataProvider as $function => $appInfo) {
    echo Html::beginTag("div", ["class" => "col-sm-5 col-md-4"]);
    echo Html::beginTag("div", ["class" => "thumbnail"]);
    $image = Html::img($appInfo[DataAnalysisAppSearch::APP_VIGNETTE_IMAGE],[
                "class" => "img-responsive",
                "alt" => $appInfo[DataAnalysisAppSearch::APP_SHORT_NAME]
                ]);
    echo Html::a( $image, $appInfo[DataAnalysisAppSearch::APP_INDEX_URL]);
    echo Html::beginTag("center");
    echo Html::tag("strong", $appInfo[DataAnalysisAppSearch::APP_DESCRIPTION]);
    echo Html::endTag("center");
    echo Html::endTag("div");
    echo Html::endTag("div");
}

echo Html::endTag("div");
echo Html::endTag("div");