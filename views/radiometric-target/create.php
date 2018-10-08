<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = Yii::t('yii', 'Add Radiometric Target');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="radiometric-target-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>