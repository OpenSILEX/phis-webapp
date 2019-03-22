<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\yiiModels\DataAnalysisAppSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch; */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Stat/Vizu Application} other{Stat/Vizu Applications}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->session->hasFlash('scriptNotAvailable')) {
    echo Html::tag("p", "Script not available", ["class" => "alert alert-danger"]);
}

echo Html::beginTag("div", ["class" => "data-analysis-index"]);
echo Html::beginTag("div", ["class" => "row"]);
foreach ($dataProvider as $function => $appInfo) {
    if ($integrated) {
        $appHref =  Url::to([
                    "data-analysis/run-script/", 
                    "function" => $function,
                    "rpackage" => $appInfo[DataAnalysisAppSearch::R_PACKAGE_NAME]
                    ]);
    } else {
        $appHref = $appInfo[DataAnalysisAppSearch::APP_INDEX_HREF];
    }
    echo Html::beginTag("div", ["class" => "col-sm-6 col-md-5"]);
    echo Html::beginTag("div", ["class" => "thumbnail"]);
    echo Html::beginTag("a", ["href" => $appHref]);
    echo Html::img($appInfo[DataAnalysisAppSearch::VIGNETTE_IMAGE],[
                "class" => "img-responsive",
                "alt" => $appInfo[DataAnalysisAppSearch::APP_SHORT_NAME]
                ]);
    echo Html::endTag("a");
    echo Html::beginTag("center");
    echo Html::tag("strong", $appInfo[DataAnalysisAppSearch::FUNCTION_HELP]);
    echo Html::endTag("center");
    echo Html::endTag("div");
    echo Html::endTag("div");
}

echo Html::endTag("div");
echo Html::endTag("div");
