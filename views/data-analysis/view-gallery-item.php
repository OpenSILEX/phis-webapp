<?php

//******************************************************************************
//                                 gallery.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use alcea\yii2PrismSyntaxHighlighter\PrismSyntaxHighlighter;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}', ['n' => 2]
);
$this->params['breadcrumbs'][] = $this->title;

PrismSyntaxHighlighter::widget([
    'theme' => PrismSyntaxHighlighter::THEME_COY,
    'languages' => ['r', 'php', 'php-extras', 'css'],
    'plugins' => ['copy-to-clipboard']
]);

echo Html::beginTag("div", ["class" => "row"]);

$descriptionFilePathExt = pathinfo(yii::getAlias($descriptionFilePath), PATHINFO_EXTENSION);
if ($descriptionFilePathExt == "html") {
    echo \Yii::$app->view->renderAjax($descriptionFilePath);
} elseif ($descriptionFilePathExt == "png" || $descriptionFilePathExt == "jpg") {
    $descriptionFilePath = str_replace('@app/web/', '', $descriptionFilePath);
    echo Html::img(\yii\helpers\Url::to($descriptionFilePath), ["class" => "img-responsive center-block"]);
}

//echo Markdown::process($html);
//echo HtmlPurifier::process($html,[
//    'HTML.AllowedAttributes' => 'src, height, width, alt',
//    'Core.AggressivelyRemoveScript' => false,
//    'HTML.Allowed', 'u,p,b,i,span[style],p,strong,em,li,ul,ol,div[align],br,img,iframe'
//    ]);
echo Html::endTag("div");
echo Html::beginTag("div", ["class" => "row"]);
echo Html::tag('h3', 'R Function Code');

$RfunctionPathContent = $this->renderFile($RfunctionPath);

$md = <<<MD_FILE
```r
$RfunctionPathContent
```
MD_FILE;

echo Markdown::process($md, 'gfm-comment');
echo Html::endTag("div");
