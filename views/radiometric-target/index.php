<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SensorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="radiometric-target-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
            if (Yii::$app->session['isAdmin']) {
                echo Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) . "\t";
            }
        ?>
    </p>
    
</div>