<?php

//******************************************************************************
//                                 gallery.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
nezhelskoy\highlight\HighlightAsset::register($this);

use yii\helpers\Html;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}', ['n' => 2]
);
$this->params['breadcrumbs'][] = $this->title;


echo Html::beginTag("div", ["class" => "row"]);

$descriptionFilePathExt = pathinfo(yii::getAlias($descriptionFilePath), PATHINFO_EXTENSION);
if ($descriptionFilePathExt == "html") {
    echo \Yii::$app->view->renderAjax($descriptionFilePath);
} elseif ($descriptionFilePathExt == "png" || $descriptionFilePathExt == "jpg") {
    $descriptionFilePath = str_replace('@app/web/', '', $descriptionFilePath);
    echo Html::img(\yii\helpers\Url::to($descriptionFilePath), ["class" => "img-responsive center-block"]);
}
echo Html::endTag("div");
echo Html::beginTag("div", ["class" => "row"]);
echo Html::tag('h3', 'R Function Code');
echo Html::beginTag("pre");
echo Html::beginTag("code", ["class" => "r"]);
echo $this->renderFile($RfunctionPath);
echo Html::endTag("code");
echo Html::endTag("pre");
echo Html::endTag("div");
