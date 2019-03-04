<?php

//******************************************************************************
//                                 index.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
//use Yii;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\Url;
//use kartik\daterange\DateRangePicker;
use app\models\yiiModels\DataAnalysisAppSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch; */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Stat/Vizu Application} other{Stat/Vizu Applications}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->session->hasFlash('scriptNotAvailable')){ ?>
<p class="alert alert-danger">Script not available</p>
<?php 
Yii::$app->session->removeFlash("scriptNotAvailable");
}
?>

<div class="data-analysis-index">
    <div class="row">
        <?php foreach ($dataProvider as $function => $appInfo) {?>
            <div class="col-sm-6 col-md-5">
                <div class="thumbnail">
                    <a href="<?= Url::to(($integrated) ? ["data-analysis/run-script/", "function" => $function, "rpackage" =>  $appInfo[DataAnalysisAppSearch::R_PACKAGE_NAME]] : $appInfo[DataAnalysisAppSearch::APP_INDEX_HREF] )?>">
                        <?= Html::img($appInfo[DataAnalysisAppSearch::VIGNETTE_IMAGE], ["class" => "img-responsive", "alt" => $appInfo[DataAnalysisAppSearch::APP_SHORT_NAME]]) ?>
                    </a>
                    <center>
                        <?= Markdown::process($appInfo[DataAnalysisAppSearch::FUNCTION_HELP]) ?>
                    </center>
                </div>
            </div>
        <?php } ?>
    </div>
</div>