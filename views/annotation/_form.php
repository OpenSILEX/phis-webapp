<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use app\models\yiiModels\YiiAnnotationModel;
use yii\grid\GridView;
use app\controllers\AnnotationController;

/**
 * @var yii\web\View $this
 * @var app\models\Annotation $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=
    $form->field($model, YiiAnnotationModel::MOTIVATED_BY)->dropDownList(
            ${AnnotationController::MOTIVATION_INSTANCES}
    );
    ?>

    <?= $form->field($model, YiiAnnotationModel::CREATION_DATE)->textInput(['readonly' => 'true']); ?>


    <?php
    // Show targets
    foreach ($model->targets as $target) {
        $targets[] = [YiiAnnotationModel::TARGETS => $target];
    }

    $dataProvider = new ArrayDataProvider([
        'allModels' => $targets,
        'pagination' => [
            'pageSize' => 10,
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            YiiAnnotationModel::TARGETS
        ],
    ]);
    ?>
    <!--input list of targets-->
    <?php
    foreach ($model->targets as $index => $target) {
        echo $form->field($model, YiiAnnotationModel::TARGETS . "[$index]")->hiddenInput(['readonly' => 'true', "value" => $target])->label(false);
    }
    ?>
    <!--//SILEX:conception
    // Think about putting image, documents and note in the annotation
    //\SILEX-->
    <!--First annotation body-->
    <?= $form->field($model, YiiAnnotationModel::COMMENTS . "[0]")->textArea(['rows' => 5]); ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
