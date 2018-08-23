<?php

//**********************************************************************************************
//                                       _form.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: creation or update user's form
//***********************************************************************************************

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

/* @var $this yii\web\View */
/* @var $model app\models\YiiUserModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="user-form well">
    <?php $form = ActiveForm::begin(); ?>
    
    <?php 
    $readonly = false;
    if (!$model->isNewRecord) {
        $readonly = true;
    }
    echo $form->field($model, 'email')->input('email', ['readonly' => $readonly]); ?>
    
    <?php if (Yii::$app->session['isAdmin'] || $model->email === Yii::$app->session['email']) { //Quand l'utilisateur connecté est un admin, il peut créer des comptes users
        echo $form->field($model, 'password')->passwordInput();
    }  ?>
    
    <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]); ?>
    
    <?= $form->field($model, 'familyName')->textInput(['maxlength' => true]); ?>
    
    <?= $form->field($model, 'phone')->widget(PhoneInput::className(), [
        'jsOptions' => [
            'preferredCountries' => ['fr'],
        ]
    ]); ?>
    
    <?= $form->field($model, 'address')->textInput(['maxlength' => true]); ?>
    
    <?= $form->field($model, 'affiliation')->textInput(['maxlength' => true, 'placeholder' => 'INRA MISTEA']); ?>
    
    <?= $form->field($model, 'orcid')->textInput(['maxlength' => true]); ?>
    
    
    <?php if (Yii::$app->session['isAdmin']) {
        echo $form->field($model, 'isAdmin')->checkbox();
    }  ?>
    
    <?php if (Yii::$app->session['isAdmin']) {
            if ($model->isNewRecord) {
                echo $form->field($model, 'groups')->widget(\kartik\select2\Select2::classname(),[
                    'data' => $this->params['listGroups'],
                    'options' => [
                        'placeholder' => 'Select groups ...',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            } else {
                
                echo $form->field($model, 'groups')->widget(\kartik\select2\Select2::classname(),[
                    'data' => $this->params['listGroups'],
                    
                    'options' => [
                        'value' => $this->params['listActualGroups'],
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
    }   ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii' , 'Create') : Yii::t('yii' , 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>