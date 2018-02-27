<?php

//**********************************************************************************************
//                                       _form.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 27 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 27 2017
// Subject: Formulaire de création ou de modification d'une variable
//***********************************************************************************************

use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelVariable app\models\YiiVariableModel */
/* @var $modelTrait app\models\YiiTraitModel */
/* @var $modelMethod app\models\YiiMethodModel */
/* @var $modelUnit app\models\YiiUnitModel */
/* @var $listTraits array */
/* @var $listMethods array */
/* @var $listUnits array */
?>

<script>
    var options = {};
    var basicOptions = {};
    $(document).ready(function() {
        $('#yiivariablemodel-label').tooltip();
//        
        $("#yiivariablemodel-ontologiesreferences-0-entity option").each(function() {
            options[$(this).val()] = $(this).text();
            basicOptions[$(this).val()] = $(this).text();
        });
        
        $('#addTrait').click(function() {
           $('#createTrait').toggle();
           if ($('#addTraitGlyphicon').attr('class') === 'glyphicon glyphicon-plus') {
               $('#addTraitGlyphicon').removeClass('glyphicon-plus').addClass('glyphicon-minus');
               $('#addTrait').removeClass('btn btn-success').addClass('btn btn-danger');
                //On remet le trait dans la liste des entities sur lesquelles on peut mettre une info
                addEntityOntologiesReferences("<?= \config::path()['cTrait'] ?>");
           } else {
               $('#addTraitGlyphicon').removeClass('glyphicon-minus').addClass('glyphicon-plus');
               $('#addTrait').removeClass('btn btn-danger').addClass('btn btn-success');
           }
        });
        
        $('#addMethod').click(function() {
           $('#createMethod').toggle();
           if ($('#addMethodGlyphicon').attr('class') === 'glyphicon glyphicon-plus') {
               $('#addMethodGlyphicon').removeClass('glyphicon-plus').addClass('glyphicon-minus');
               $('#addMethod').removeClass('btn btn-success').addClass('btn btn-danger');
               addEntityOntologiesReferences("<?= \config::path()['cMethod'] ?>");
           } else {
               $('#addMethodGlyphicon').removeClass('glyphicon-minus').addClass('glyphicon-plus');
               $('#addMethod').removeClass('btn btn-danger').addClass('btn btn-success');
           }
        });
        
        $('#addUnit').click(function() {
           $('#createUnit').toggle();
           if ($('#addUnitGlyphicon').attr('class') === 'glyphicon glyphicon-plus') {
               $('#addUnitGlyphicon').removeClass('glyphicon-plus').addClass('glyphicon-minus');
               $('#addUnit').removeClass('btn btn-success').addClass('btn btn-danger');
               addEntityOntologiesReferences("<?= \config::path()['cUnit'] ?>");
           } else {
               $('#addUnitGlyphicon').removeClass('glyphicon-minus').addClass('glyphicon-plus');
               $('#addUnit').removeClass('btn btn-danger').addClass('btn btn-success');
           }
        });
        
        jQuery('#multiple-input').on('afterAddRow', function(e, row) {
            $(row).find("select").first().empty();
            $.each(options, function(key, value) {
                    $(row).find("select").first().append($("<option></option>")
                        .attr("value", key).text(value));
                }); 
        });
    });
    
    function addEntityOntologiesReferences(entityURI) {        
        $.each(basicOptions, function(key, value) {
             if (key === entityURI) {
                options[key] = basicOptions[key];
            }
        });
        
        $('#multiple-input > table > tbody > tr').each(function(i, row) {
            $('#yiivariablemodel-ontologiesreferences-' + i + '-entity').empty();
            $.each(options, function(key, value) {
                $('#yiivariablemodel-ontologiesreferences-' + i + '-entity').append($("<option></option>")
                    .attr("value", key).text(value));
            });
        });
    }
    
    function hideEntityOntologiesReferences(inputType) {
        var oldOptions = {};
        $("#yiivariablemodel-ontologiesreferences-0-entity option").each(function() {
            oldOptions[$(this).val()] = $(this).text();
        });

        options = {};
        $.each(oldOptions, function(key, value) {
             if (key !== inputType) {
                options[key] = oldOptions[key];
            }
        });
        
        $('#multiple-input > table > tbody > tr').each(function(i, row) {
            $('#yiivariablemodel-ontologiesreferences-' + i + '-entity').empty();
            $.each(options, function(key, value) {
                $('#yiivariablemodel-ontologiesreferences-' + i + '-entity').append($("<option></option>")
                    .attr("value", key).text(value));
            });
        });
    }
    
    function setEntitiesOptions() {  
       $('#multiple-input > table > tbody > tr').each(function(i, row) {
            $('#yiivariablemodel-ontologiesreferences-' + i + '-entity').empty();
            $.each(options, function(key, value) {
                $('#yiivariablemodel-ontologiesreferences-' + i + '-entity').append($("<option></option>")
                    .attr("value", key).text(value));
            });
        });
    }
    
    function updateVariableLabel(inputId) {
        var variableLabel = $('#yiivariablemodel-label').val();
        var variableLabelTab = variableLabel.split("_");
            
        if (inputId === "trait") {
            var toAdd = $("#" + inputId + " option:selected").text();
            toAdd = toAdd.replace("_", "-");
            $('#yiivariablemodel-label').val(toAdd + "_" + variableLabelTab[1] + "_" + variableLabelTab[2]);
        } else if (inputId === "newtrait") { 
            var toAdd = $("#" + inputId).val(); 
            toAdd = toAdd.replace("_", "-");   
            $('#yiivariablemodel-label').val(toAdd + "_" + variableLabelTab[1] + "_" + variableLabelTab[2]);
        } else if (inputId === "method") {
            var toAdd = $("#" + inputId + " option:selected").text();
            toAdd = toAdd.replace("_", "-");
            $('#yiivariablemodel-label').val(variableLabelTab[0] + "_" + toAdd + "_" + variableLabelTab[2]);
        } else if (inputId === "newmethod") {
            var toAdd = $("#" + inputId).val();
            toAdd = toAdd.replace("_", "-");
            $('#yiivariablemodel-label').val(variableLabelTab[0] + "_" + toAdd + "_" + variableLabelTab[2]);
        } else if (inputId === "unit") {
            var toAdd = $("#" + inputId + " option:selected").text();
            toAdd = toAdd.replace("_", "-");
            $('#yiivariablemodel-label').val(variableLabelTab[0] + "_" + variableLabelTab[1] + "_" + toAdd);
        } else if (inputId === "newunit") {
            var toAdd = $("#" + inputId).val();
            toAdd = toAdd.replace("_", "-");
            $('#yiivariablemodel-label').val(variableLabelTab[0] + "_" + variableLabelTab[1] + "_" + toAdd);
        }
    }
</script>

<div class="variable-form">
    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row">
    <?php if ($modelVariable->isNewRecord) {
            echo $form->field($modelVariable, 'label')->textInput([
                //TODO Ajouter autogénération avec trait methode unité (labels)
                'readonly' => true,
                'value' => 'NA_NA_NA',
                'style' => 'background-color:#C4DAE7;',
                'data-toggle' => 'tooltip',
                'title' => 'Automatically generated',
                'data-placement' => 'left',
            ]);
          } else {
            echo $form->field($variableModel, 'label')->textInput([
                'readonly' => true,
                'style' => 'background-color:#C4DAE7;'
            ]); 
          }
    ?>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <h2><?= Yii::t('app', 'Trait') ?></h2>
            <div class="row">
                <?= $form->field($modelVariable, 'trait')->widget(\kartik\select2\Select2::classname(), [
                    'data' =>$listTraits,
                    'size' => \kartik\select2\Select2::MEDIUM,
                    'options' => ['placeholder' => 'Trait label',
                                  'multiple' => false,
                                  'onChange' => 'updateVariableLabel("trait");hideEntityOntologiesReferences("' .  \config::path()['cTrait'] . '")',
                                  'id' => 'trait'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    'addon' => [
                        'append' => [
                            'content' => Html::button('<span class="glyphicon glyphicon-plus" aria-hidden="true" id="addTraitGlyphicon"></span>', [
                                'class' => 'btn btn-success',
                                'title' => Yii::t('app/messages', 'Add new trait'),
                                'data-toogle' => 'tooltip',
                                'id' => 'addTrait'
                            ]),
                            'asButton' => true
                        ]
                    ]
                ])->label(false); ?>
                <div id="createTrait" style="display:none">
                    <?= $form->field($modelTrait, 'label')->textInput(['onChange' => 'updateVariableLabel("newtrait")', 'id' => 'newtrait']) ?>
                    <?= $form->field($modelTrait, 'comment')->textarea(['row' => '3']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-md-offset-1">
            <h2><?= Yii::t('app', 'Method') ?></h2>
            <div class="row">
                <?= $form->field($modelVariable, 'method')->widget(\kartik\select2\Select2::classname(), [
                    'data' =>$listMethods,
                    'size' => \kartik\select2\Select2::MEDIUM,
                    'options' => ['placeholder' => 'Method label',
                                  'multiple' => false,
                                  'onChange' => 'updateVariableLabel("method");hideEntityOntologiesReferences("' .  \config::path()['cMethod'] . '")',
                                  'id' => 'method'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],     
                    'addon' => [
                        'append' => [
                            'content' => Html::button('<span class="glyphicon glyphicon-plus" aria-hidden="true" id="addMethodGlyphicon"></span>', [
                                'class' => 'btn btn-success',
                                'title' => Yii::t('app/messages', 'Add new method'),
                                'data-toogle' => 'tooltip',
                                'id' => 'addMethod'
                            ]),
                            'asButton' => true
                        ]
                    ]
                ])->label(false); ?>
                <div style="display:none" id="createMethod">
                    <?= $form->field($modelMethod, 'label')->textInput(['onChange' => 'updateVariableLabel("newmethod")', 'id' => 'newmethod']) ?>
                    <?= $form->field($modelMethod, 'comment')->textarea(['row' => '3']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-md-offset-1">
            <h2><?= Yii::t('app', 'Unit') ?></h2>
            <div class="row">
            <?= $form->field($modelVariable, 'unit')->widget(\kartik\select2\Select2::classname(), [
                    'data' =>$listUnits,
                    'size' => \kartik\select2\Select2::MEDIUM,
                    'options' => ['placeholder' => 'Unit label',
                                  'multiple' => false,
                                  'onChange' => 'updateVariableLabel("unit");hideEntityOntologiesReferences("' .  \config::path()['cUnit'] . '")',
                                  'id' => 'unit'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    'addon' => [
                        'append' => [
                            'content' => Html::button('<span class="glyphicon glyphicon-plus" aria-hidden="true" id="addUnitGlyphicon"></span>', [
                                'class' => 'btn btn-success',
                                'title' => Yii::t('app/messages', 'Add new unit'),
                                'data-toogle' => 'tooltip',
                                'id' => 'addUnit'
                            ]),
                            'asButton' => true
                        ]
                    ]
                ])->label(false); ?>
                <div style="display:none" id="createUnit">
                    <?= $form->field($modelUnit, 'label')->textInput(['onChange' => 'updateVariableLabel("newunit")', 'id' => 'newunit']) ?>
                    <?= $form->field($modelUnit, 'comment')->textarea(['row' => '3']) ?>
                </div>
            </div>
        </div>
    </div> 
    <div class="row">
        <p><strong style="color:red">Warning</strong> <?= Yii::t('app/messages', 'You <b>cannot modify already existing</b> traits, methods and units.'); ?></p>
    </div>
    <div class="row">
        <h2><?= Yii::t('app', 'Ontologies References') ?></h2>
    </div>
    <div class="row">
        </i> In order to fill onotological references (<?= Html::a(Yii::t('app', 'URI'), "https://fr.wikipedia.org/wiki/Uniform_Resource_Identifier#Principe", [ 'title' => 'Uniform Resource Identifier', 'target' => '_blank']) ?>) you can go to these ontologies  :
        <ul>
            <li><?= Html::a("AGROPORTAL", "http://agroportal.lirmm.fr/search", ['target' => '_blank']) ?>
                <i class="glyphicon glyphicon-question-sign" style="color: #337ab7" title="<?=Yii::t('app/messages', 'AgroPortal')?>"></i>
            </li>
            <li><?= Html::a("AGROVOC", "http://artemide.art.uniroma2.it:8081/agrovoc/agrovoc/en/?clang=fr", ['target' => '_blank']) ?>
                <i class="glyphicon glyphicon-question-sign" style="color: #337ab7" title="<?=Yii::t('app/messages', 'AgroVoc')?>"></i>
            </li>
            <li><?= Html::a("PLANT ONTOLOGY", "http://www.ontobee.org/ontology/po", ['target' => '_blank']) ?>
                <i class="glyphicon glyphicon-question-sign" style="color: #337ab7" title="<?=Yii::t('app/messages', 'PlantOntology')?>"></i>
            </li>
            <li><?= Html::a("PLANTEOME", "http://planteome.org", ['target' => '_blank']) ?>
                <i class="glyphicon glyphicon-question-sign" style="color: #337ab7" title="<?=Yii::t('app/messages', 'Planteome')?>"></i>
            </li>
            <li><?= Html::a("CROP ONTOLOGY", "http://www.cropontology.org/", ['target' => '_blank']) ?>  
                <i class="glyphicon glyphicon-question-sign" style="color: #337ab7" title="<?=Yii::t('app/messages', 'CropOntology')?>"></i>
            </li>
            <li><?= Html::a("UNIT ONTOLOGY", "http://www.ontobee.org/ontology/UO", ['target' => '_blank']) ?>  
                <i class="glyphicon glyphicon-question-sign" style="color: #337ab7" title="<?=Yii::t('app/messages', 'UnitOntology')?>"></i>
            </li>
        </ul>
    </div>
    <div class="row">
        <?= $form->field($modelVariable, 'ontologiesReferences')->widget(MultipleInput::className(), [
            'id' => 'multiple-input',
          'columns' => [
              [
                  'name' => 'entity', 
                  'type' => 'dropDownList',
                  'title' => Yii::t('app', 'Entity'),
                  'items' => $modelVariable->getEntitiesConceptsLabels()
              ],
              [
                  'name' => 'property',
                  'type' => 'dropDownList',
                  'title' => Yii::t('app', 'Relation'),
                  'items' => $modelVariable->getEntitiesPossibleRelationsToOthersConcepts()
              ],
              [
                  'name' => 'object',
                  'title' => Yii::t('app', 'Reference URI'),
                  'enableError' => true,
                  'options' => [
                    'class' => 'input-priority',
                   ]
              ],
              [
                  'name' => 'seeAlso',
                  'title' => Yii::t('app', 'Hyperlink'),
                  'enableError' => true,
                  'options' => [
                    'class' => 'input-priority',
                   ]
              ],
          ]  
        ]) ?>
    </div>
    
    <div class="row">
        <?= $form->field($modelVariable, 'comment')->textarea(['rows' => 6]) ?>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton($modelVariable->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $modelVariable->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>