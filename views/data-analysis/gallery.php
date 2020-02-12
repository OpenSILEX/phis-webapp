<?php

//******************************************************************************
//                                 gallery.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}', ['n' => 2]
);
$this->params['breadcrumbs'][] = $this->title;

//var_dump($dataProvider);exit;
echo Html::beginTag("div", ["class" => "data-analysis-index"]);
echo Html::beginTag("div", ["class" => "row"]);
foreach ($dataProvider as $category => $categoryInfo) {
    echo Html::beginTag("div", ["class" => "row"]);
    $numberOfItems = count(($categoryInfo['items']));
    echo Html::tag("hr");
    echo Html::tag("h2", Yii::t('app', $categoryInfo["label"]) . ' (' . $numberOfItems . ')');
    echo Html::tag("hr");

    foreach ($categoryInfo['items'] as $categoryItem => $categoryItemInfo) {

        echo Html::beginTag("div", ["class" => "col-sm-4 col-md-3"]);
        echo Html::beginTag("div", ["class" => "thumbnail"]);
        $image = Html::img('RGallery/' . $category . '/' . $categoryItem . '/vignette.png', [
                    "class" => "img-responsive",
                    "alt" => $categoryItem
        ]);
        $exampleUrl = Url::to(
                        [
                            'data-analysis/view-gallery-item',
                            'descriptionFilePath' => $galleryFilePath . '/' . $category . '/' . $categoryItemInfo['descriptionFilePath'],
                            'RfunctionPath' => $galleryFilePath . '/' . $category . '/' . $categoryItemInfo['RfunctionPath'],
                        ]
        );
        echo Html::a($image, $exampleUrl);
        echo Html::beginTag("center");
        echo Html::tag("strong", Yii::t('app/messages', $categoryItem));
        echo Html::endTag("center");
        echo Html::endTag("div");
        echo Html::endTag("div");
    }
    echo Html::endTag("div");
}

echo Html::endTag("div");
echo Html::endTag("div");
