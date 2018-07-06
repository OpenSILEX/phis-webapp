<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\Annotation $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin();?>

    <?php echo  $form->field($model, 'motivatedBy')->dropDownList(
        $motivationIndividuals
    );
    ?>
    

    <?php echo $form->field($model, 'creationDate')->textInput(['readonly' => 'true']); ?>



    <?php echo $form->field($model, 'target')->textInput(['readonly'=> 'true']); ?>

    <?php echo $form->field($model, 'comment')->textArea(['rows' => 5]); ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
