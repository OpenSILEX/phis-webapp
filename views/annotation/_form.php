<?php
//******************************************************************************
//                           _form.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 6 Aug, 2017
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use app\models\yiiModels\YiiAnnotationModel;
use yii\grid\GridView;
use app\controllers\AnnotationController;

/**
 * @var yii\web\View $this
 * @var app\models\yiiModels\YiiAnnotationModel $model
 * @var yii\widgets\ActiveForm $form
 * Implements the create page for an annotation
 * @see app\views\annotation\create.php
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
        // return url after annotation creation
        echo $form->field($model, YiiAnnotationModel::RETURN_URL)->hiddenInput(['readonly' => 'true'])->label(false);
    ?>
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
          ['label' => Yii::t('app',YiiAnnotationModel::TARGETS_LABEL),
          'attribute' => YiiAnnotationModel::TARGETS]
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
    <?= $form->field($model, YiiAnnotationModel::BODY_VALUES . "[0]")->textArea(['rows' => 5]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
