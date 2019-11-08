<?php

//**********************************************************************************************
//                                       _form.php
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  January, 15 2018 (multiple variables dataset)
// Subject: creation of dataset via CSV
//***********************************************************************************************

use Yii;
use yii\helpers\Html;
use unclead\multipleinput\MultipleInput;
use kartik\form\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\YiiDatasetModel */
/* @var $form yii\widgets\ActiveForm */
/* @var $handsontable openSILEX\handsontablePHP\adapter\HandsontableSimple */
/* @var $handsontableErrorsCellsSettings string */

 if ($handsontable !== null) {
     echo $handsontable->loadJSLibraries(true);
     echo $handsontable->loadCSSLibraries();
 }

?>

<div class="dataset-form well">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= Yii::$app->session->getFlash('renderArray'); ?>

    <?php
        if (isset($errors) && $errors !== null):
    ?>
    <div class="alert alert-danger" >
        <h3 style="margin:3%;">Errors found in dataset :</h3>
        <ul>
        <?php 
            // Display error messages
            $errorMessages = [];
            foreach($errors as $error) {
                if (is_string($error)) {
                    $errorMessages[] =  $error;
                } else {
                    $errorMessages[] =  $error->exception->details;
                }
            }
            
            $errorMessages = array_unique($errorMessages);
            foreach ($errorMessages as $errorMessage) {
                 echo '<li>' . $errorMessage . '</li>';
            }
            
        ?>
        </ul>
    </div>
    <?php
        endif;
    ?>
    <?php
        if (isset($handsontable) && $handsontable !== null):
    ?>
        <div id="errors">

            <h3 class="alert alert-danger" style="margin:3%;">Errors found in dataset </h3>

                <div id="<?= $handsontable->getContainerName() ?>">
                </div>
                <script>
                    <?= $handsontable->generateJavascriptCode(); ?>
                    <?= $handsontableErrorsCellsSettings; ?>
                </script>

        </div>

        <h3 class="alert alert-info" style="margin:3%;">Add dataset form</h3>
    <?php endif; ?>
    <?= $form->field($model, 'experiment')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['experiments'],
                'options' => [
                    'placeholder' => Yii::t('app/messages', 'Select one experiment') . ' ...',
                    'id' => 'experiment-selector',
                    'multiple' => false
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true
                ],
            ]); ?>
        
    <?= $form->field($model, 'variables')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['variables'],
                'options' => [
                    'placeholder' => Yii::t('app/messages', 'Select one or many variables') . ' ...',
                    'id' => 'uriVariable-selector',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true
                ],
            ]); ?>

    <hr style="border-color : gray;"/>
    <h3><?= Yii::t('app', 'Provenance')?></h3>

    <script>
        $(document).ready(function() {
            <?php
                // Create Provenance select values array
                foreach ($this->params['provenances'] as $uri => $provenance) {
                    $provenancesArray[$uri] = $provenance->label . " (" . $uri . ")";
                }
                // Inject URI to get document widget linked to an URI
                echo 'var documentsLoadUri = "' . Url::to(['document/get-documents-widget']) . '";';
                // Inject provenances list indexed by URI
                echo 'var provenances = ' . json_encode($this->params['provenances']) . ';';
                 // Inject sensingDevices list indexed by URI
                echo 'var sensingDevices = ' . json_encode($this->params['sensingDevices']) . ';';
                 // Inject agents list indexed by URI
                echo 'var agents = ' . json_encode($this->params['agents']) . ';';
            ?>
                
            // Function to update provenance comment field depending of selected URI
            var updateProvenanceFields = function(uri) {
                if (provenances.hasOwnProperty(uri)) {
//                    console.log(provenances[uri]);
                    // If selected provenance is known get its comment
                    var comment = provenances[uri]["comment"];
                    
                    // Set provenance comment, disable input and remove validation messages 
                    $("#yiidatasetmodel-provenancecomment").val(comment).attr("disabled", "disabled");
                    $(".field-yiidatasetmodel-provenancecomment, .field-yiidatasetmodel-provenancecomment *")
                        .removeClass("has-error")
                        .removeClass("has-success");
                    $(".field-yiidatasetmodel-provenancecomment .help-block").empty();
                    
                    // Set provenance provenanceSensingDevices, disable input and remove validation messages 
                    try {
                        var provenanceSensingDevices = provenances[uri]["metadata"]["prov:Agent"]["oeso:SensingDevice"];
                        $("#yiidatasetmodel-provenancesensingdevices").val(provenanceSensingDevices);
                        $("#yiidatasetmodel-provenancesensingdevices").trigger("change");
//                        console.log(provenanceSensingDevices);
                    }
                    catch(error) {
                        $("#yiidatasetmodel-provenancesensingdevices").val([]);
                        $("#yiidatasetmodel-provenancesensingdevices").trigger("change");
                        console.log("No sensing device set");
                    }finally {
                        $("#yiidatasetmodel-provenancesensingdevices").val(provenanceSensingDevices).attr("disabled", "disabled");
                        $(".field-yiidatasetmodel-provenancesensingdevices, .field-yiidatasetmodel-provenancesensingdevices *")
                            .removeClass("has-error")
                            .removeClass("has-success");
                        $(".field-yiidatasetmodel-provenancesensingdevices .help-block").empty();
                    }
                    // Set provenance provenanceSensingDevices, disable input and remove validation messages 
                    try {
                        var provenanceAgents = provenances[uri]["metadata"]["prov:Agent"]["oeso:Operator"];
                        $("#yiidatasetmodel-provenanceagents").val(provenanceAgents);
                        $("#yiidatasetmodel-provenanceagents").trigger("change");
//                        console.log(provenanceAgents);
                    }
                    catch(error) {
                         $("#yiidatasetmodel-provenanceagents").val([]);
                         $("#yiidatasetmodel-provenanceagents").trigger("change");
                         console.log("No agents set");
                    }finally {
                        $("#yiidatasetmodel-provenanceagents").val(agents).attr("disabled", "disabled");
                        $(".field-yiidatasetmodel-provenanceagents, .field-yiidatasetmodel-provenanceagents *")
                            .removeClass("has-error")
                            .removeClass("has-success");
                        $(".field-yiidatasetmodel-provenanceagents .help-block").empty();
                    }
                    // Load linked documents
                    $("#already-linked-documents").load(documentsLoadUri, {
                        "uri": uri
                    })
                } else {
                    // Otherwise clear provenance comment and enable input
                    $("#yiidatasetmodel-provenancesensingdevices").val(sensingDevices).removeAttr("disabled").trigger("change");
                    
                    // Otherwise clear provenance comment and enable input
                    $("#yiidatasetmodel-provenanceagents").val(agents).removeAttr("disabled").trigger("change");
                    
                    // Otherwise clear provenance comment and enable input
                    $("#yiidatasetmodel-provenancecomment").val("").removeAttr("disabled");
                    
                    // Clear linked documents list
                    $("#already-linked-documents").empty();
                }    
            }
            
            // On provenance change update provenance fields
            $("#provenance-selector").change(function() {
                updateProvenanceFields($(this).val());
            });
            
            // Update provenance fields depending of startup value
            updateProvenanceFields($("#provenance-selector").val());
        });
    </script>
    <?= $form->field($model, 'provenanceUri')->widget(\kartik\select2\Select2::classname(),[
                'data' => $provenancesArray,
                'options' => [
                    'placeholder' => Yii::t('app/messages', 'Select existing provenance or create a new one') . ' ...',
                    'id' => 'provenance-selector',
                    'multiple' => false
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true
                ],
            ]); ?>

      <?= $form->field($model, 'provenanceSensingDevices')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['sensingDevices'],
                'options' => [
                    'placeholder' => Yii::t('app/messages', 'Select existing device') . ' ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'tags' => true
                ],
            ]); ?>
    
    <?= $form->field($model, 'provenanceAgents')->widget(\kartik\select2\Select2::classname(),[
                'data' => $this->params['agents'],
                'options' => [
                    'placeholder' => 'Select operator ...',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

    <?= $form->field($model, 'provenanceComment')->textarea(['rows' => 6]) ?>
    
    <h3><?= Yii::t('app', 'Linked Document(s)') ?></h3>
    <div id="already-linked-documents"></div>
   <?=  $form->field($model, 'documentsURIs')->widget(MultipleInput::className(), [
        'max'               => 6,
        'allowEmptyList'    => true,
        'enableGuessTitle'  => true,
        'columns' => [
            [
                'name' => 'documentURI',
                'options' => [
                  'readonly' => true,
                  'style' => 'background-color:#C4DAE7;',
                 ]
            ]
        ],
        'addButtonOptions' => [
            'class' => 'btn btn-primary buttonDocuments',
            'label' => Yii::t('app', 'Add Document')
        ],
    ])->label(false)
    ?>



    <hr style="border-color : gray;"/>

      <div class="alert alert-info" role="alert">
        <b><?= Yii::t('app/messages', 'File Rules')?> : </b>
        <ul>
            <li><?= Yii::t('app/messages', 'CSV separator must be')?> "<b><?= Yii::$app->params['csvSeparator']?></b>"</li>
            <li><?= Yii::t('app/messages', 'Decimal separator for numeric values must be')?> "<b>.</b>"</li>
        </ul>
        <br/>
        <b><?= Yii::t('app', 'Columns')?> : </b>
        <table class="table table-hover" id="dataset-csv-columns-desc">
            <tr>
                <th style="color:red">ScientificObjectAlias *</th>
                <td><?= Yii::t('app/messages', 'The ALIAS of the scientific object for the choosen experiment (e.g MTP_WW_2019_P050_SH1_2018_LF14)')?></td>
            </tr>
            <tr>
                <th style="color:red">Date *</th>
                <td><p><?= Yii::t('app/messages', 'Acquisition date of the data') ?> (format ISO 8601: YYYY-MM-DD or YYYY-MM-DDTHH:mm:ssZ) </p> </td>
            </tr>
             <tr class="dataset-variables">
                <th style="color:red">Value *</th>
                <td ><?= Yii::t('app', 'Value') ?> (<?= Yii::t('app', 'Real number, String or Date') ?>)</td>
            </tr>
        </table>
    </div>

    <p>
        <?php 
            $csvPath = "coma";
            if (Yii::$app->params['csvSeparator'] == ";") {
                $csvPath = "semicolon";
            }
        ?>
        <i><?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Template'), \config::path()['basePath'] . 'documents/DatasetFiles/' . $csvPath . '/datasetTemplate.csv', ['id' => 'downloadDatasetTemplate']) ?></i>
        <i style="float: right"><?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Example'), \config::path()['basePath'] . 'documents/DatasetFiles/' . $csvPath . '/datasetExemple.csv') ?></i>
    </p>
    <?= $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => [
            'maxFileSize' => 2000,
            'pluginOptions'=>['allowedFileExtensions'=>['csv'],'showUpload' => false],
        ]
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii' , 'Create') , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="modal fade" id="document-modal" tabindex="-1" role="dialog" aria-labelledby="document-modal-title">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="document-modal-title"><?php echo Html::encode(Yii::t('app', 'Create Document')); ?></h4>
                </div>
                <div class="modal-body">
                    <div id="document-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="document-save"><?php echo Html::encode(Yii::t('yii', 'Create')) ?></button>
                     <button type="button" class="btn btn-default modal-close" data-dismiss="modal"><?php echo Html::encode(Yii::t('yii', 'Close')) ?></button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function(){
        // Load document add form popin
        $('#document-content').load('<?php echo Url::to(['document/create-from-dataset']) ?>');
        
        var documentUploaded = false;
        // Clear last uploaded document line if document add form is canceled
        $('#document-modal').on('hidden.bs.modal', function () {
            if (!documentUploaded) {
                $(".js-input-remove:last").click();
            }
            documentUploaded = false;
            
            $('#document-content form').trigger('reset');

            $('#document-save-msg').parent().removeClass('alert-danger');
            $('#document-save-msg').parent().addClass('alert-info');
            $('#document-save-msg').html('<?php echo Yii::t('app/messages', 'Request text'); ?>');
        });

        $(document).on('change', '#uriVariable-selector', function() {
            $(".dataset-variables").remove();
              $("#uriVariable-selector :selected").each(function (i,sel) {
                    $('#dataset-csv-columns-desc').append(
                            '<tr class="dataset-variables">'
                                + '<th style="color:red" >'
                                    + $(sel).text() + ' *'
                                + '</th>'
                                + '<td>'
                                    + '<?php echo Yii::t("app/messages", "Value"); ?> (<?php echo Yii::t('app', 'Real number, String or Date'); ?>)'
                                + '</td>'
                            + '</tr>');
                  });
        });

        // Initial document count
        var nbDocuments = -1;
        $(document).on('click', '#document-save', function () {
                // On save get document form values
                var formData = new FormData();
                var file_data = $('#document-content #yiidocumentmodel-file').prop('files')[0];
                formData.append('file', file_data);
                var other_data = $('form').serializeArray();
                $.each(other_data, function(key, input) {
                    formData.append(input.name, input.value);
                });

                // Send documents form
                $.ajax({
                    url: 'index.php?r=document%2Fcreate-from-dataset',
                    type: 'POST',
                    processData: false,
                    datatype: 'json',
                    contentType: false,
                    data: formData

                })
                .done(function (data) {
                    // Add document URI and close document add form
                    $('#yiidatasetmodel-documentsuris-documenturi-' + nbDocuments).val(data);
                    documentUploaded = true;
                    $('#document-modal').modal('toggle');

                })
                .fail(function (jqXHR, textStatus) {
                    // Disaply errors
                    $('#document-save-msg').parent().removeClass('alert-info');
                    $('#document-save-msg').parent().addClass('alert-danger');
                    $('#document-save-msg').html('Request failed: ' + textStatus);
                });
                
                return false;
        });

        // Open document add form on click
        $('.buttonDocuments').click(function() {
            nbDocuments++;
            typeInsertedDocument = "document";
            $('#document-modal').modal('toggle');
        });

        // Download adjusted to variables CSV template file on click
        $(document).on('change', '#uriVariable-selector', function () {
            var variablesLabels = [];
            $("#uriVariable-selector :selected").each(function (i,sel) {
                variablesLabels.push($(sel).text());
              });
            $.ajax({
                url: 'index.php?r=dataset%2Fgenerate-and-download-dataset-creation-file',
                type: 'POST',
                datatype: 'json',
                data: {variables: variablesLabels}
            })
                    .done(function (data) {
                    })
                    .fail(function (jqXHR, textStatus) {
                        $('#document-save-msg').parent().removeClass('alert-info');
                        $('#document-save-msg').parent().addClass('alert-danger');
                        $('#document-save-msg').html('Request failed: ' + textStatus);
                    });
        });

    });
    </script>
</div>
