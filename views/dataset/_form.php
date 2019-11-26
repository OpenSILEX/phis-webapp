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
        <h3 style="margin:3%;">Errors found in datasensor :</h3>
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
    <h3><i>  <?= Yii::t('app', 'Steps to insert your dataset') ?></i></h3>
  <h3><i> <a data-toggle="collapse" href="#step-01" role="button" aria-expanded="false" aria-controls="collapseDataSetRules">
             <?= "1. " . Yii::t('app', 'Choose the experiment from where the dataset comes from') ?> <span style="color:red"> *</span></a></i></h3>
    <div class="collapse" id="step-01" data-step="1">
        <?=
        $form->field($model, 'experiment')->widget(\kartik\select2\Select2::classname(), [
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
            'pluginEvents' => [
                'select2:select' => 'function(e) { populateVariableList(e.params.data.id); }',
            ]
        ]);
        ?>
    </div>
  
    <h3><i> <a data-toggle="collapse" href="#step-02" role="button" aria-expanded="false" aria-controls="collapseDataSetRules">
             <?= "2. " . Yii::t('app', 'Do you want to generate a dataset template ?') ?> </a></i></h3>
    <div class="collapse" id="step-02" data-step="2">
    <hr>
    <hr style="border-color:gray;"/>
    <h3><i>  <?= Yii::t('app', 'Dataset template generation') ?></i></h3>
    <p class="alert alert-info"><?= Yii::t('app/messages', 'The variables below are associated to the choosen experiment'); ?></p>
    <?php
    $select2VariablesOptions =  [
            'placeholder' => Yii::t('app/messages', 'Select one or many experiment associated variables') . ' ...',
            'id' => 'uriVariable-selector',
            'multiple' => true
        ]; 
    ?>
    
    <?=
    $form->field($model, 'variables')->widget(\kartik\select2\Select2::classname(), [
        'data' => [],
        'options' => $select2VariablesOptions,
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true
        ],
    ]);
    ?>
    <p>
<?php
$csvPath = "coma";
if (Yii::$app->params['csvSeparator'] == ";") {
    $csvPath = "semicolon";
}
?>
    <i>
    <?= 
        Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download generated template'),
            \config::path()['basePath'] . 'documents/DatasetFiles/' . $csvPath . '/datasetTemplate.csv',
            ['id' => 'downloadDatasetTemplate']
        );
    ?>
    </i>
    </p>
   
    <hr>
    <hr style="border-color:gray;"/>
    <i style="float: right"><?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Example'), \config::path()['basePath'] . 'documents/DatasetFiles/' . $csvPath . '/datasetExemple.csv') ?></i>
        <br>
        <div class="alert alert-info" role="alert">
            <b><?= Yii::t('app/messages', 'File Rules') ?> : </b>
            <ul>
                <li><?= Yii::t('app/messages', 'CSV separator must be') ?> "<b><?= Yii::$app->params['csvSeparator'] ?></b>"</li>
                <li><?= Yii::t('app/messages', 'Decimal separator for numeric values must be') ?> "<b>.</b>"</li>
            </ul>
            <br/>
            <b><?= Yii::t('app', 'Columns') ?> : </b>
            <table class="table table-hover" id="dataset-csv-columns-desc">
                <tr>
                    <th style="color:red">ScientificObjectAlias *</th>
                    <td><?= Yii::t('app/messages', 'The ALIAS of the scientific object for the choosen experiment (e.g MTP_WW_2019_P050_SH1_2018_LF14)') ?></td>
                </tr>
                <tr>
                    <th style="color:red">Date *</th>
                    <td><p><?= Yii::t('app/messages', 'Acquisition date of the data') ?> (format ISO 8601: YYYY-MM-DD or YYYY-MM-DDTHH:mm:ssZ) </p> </td>
                </tr>
                <tr class="dataset-variables">
                    <th style="color:red">Variable value *</th>
                    <td ><?= Yii::t('app', 'Variable value') ?> (<?= Yii::t('app', 'Real number, String or Date') ?>)</td>
                </tr>
            </table>
        </div>
    </div>
      <h3><i> <a data-toggle="collapse" href="#step-03" role="button" aria-expanded="false" aria-controls="collapseDataSetRules">
             <?= "3. " . Yii::t('app', 'Select or create a source that describes how the dataset was obtained') ?><span style="color:red"> *</span> </a></i></h3>
    <div class="collapse" id="step-03" data-step="3">
    <h4><?= Yii::t('app', 'Provenance'); ?></h4>
    <p class="alert alert-info"><?= Yii::t('app/messages', 'To create a new provenance, write the provenance label in the research field and press `Enter`'); ?></p>
    <script>
    $(document).ready(function () {
<?php
// Create Provenance select value<div class="collapse" id="collapseExample">s array
foreach ($this->params['provenances'] as $uri => $provenance) {
    $provenancesArray[$uri] = $provenance->label . " (" . $uri . ")";
}
// Inject URI to get document widget linked to an URI
echo 'documentsLoadUri = "' . Url::to(['document/get-documents-widget']) . '";';
// Inject provenances list indexed by URI
echo 'provenances = ' . json_encode($this->params['provenances']) . ';';
// Inject sensingDevices list indexed by URI
echo 'sensingDevices = ' . json_encode($this->params['sensingDevices']) . ';';
// Inject agents list indexed by URI
echo 'agents = ' . json_encode($this->params['agents']) . ';';
?>

        // On provenance change update provenance fields
        $("#provenance-selector").change(function () {
            updateProvenanceFields($(this).val());
        });

        // Update provenance fields depending of startup value
        updateProvenanceFields($("#provenance-selector").val());

    });
    </script>
    <?=
    $form->field($model, 'provenanceUri')->widget(\kartik\select2\Select2::classname(), [
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
    ]);
    ?>

    <?=
    $form->field($model, 'provenanceAgents')->widget(\kartik\select2\Select2::classname(), [
        'data' => $this->params['agents'],
        'options' => [
            'placeholder' => 'Select operator ...',
            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'provenanceComment')->textarea(['rows' => 6]) ?>

    <h3><?= Yii::t('app', 'Linked Document(s)') ?></h3>
    <div id="already-linked-documents"></div>
    <?=
    $form->field($model, 'documentsURIs')->widget(MultipleInput::className(), [
        'max' => 6,
        'allowEmptyList' => true,
        'enableGuessTitle' => true,
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
    <?= Html::button(Yii::t('yii', 'Save provenance'), ['class' => 'btn btn-success','onclick' => 'saveProvenance()']) ?>

    </div>
    <h3><i> <a data-toggle="collapse" href="#step-04" role="button" aria-expanded="false" aria-controls="collapseDataSetRules">
             <?= "4. " . Yii::t('app', 'Save your dataset.') ?> </a></i></h3>
    <div class="collapse" id="step-04" data-step="4">
    <hr style="border-color:gray;"/>    
    <h3><i>  <?= Yii::t('app', 'Dataset input file') ?></i></h3>

    <?=
    $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => [
            'maxFileSize' => 2000,
            'pluginOptions' => ['allowedFileExtensions' => ['csv'], 'showUpload' => false],
        ]
    ]);
    ?>

    <div class="form-group">
    <?= Html::submitButton(Yii::t('yii', 'Save dataset'), ['class' => 'btn btn-success']) ?>
    </div>
    </div>
<?php ActiveForm::end(); ?>
    <div>
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
        $(document).ready(function () {
            
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

            $(document).on('change', '#uriVariable-selector', function () {
                $(".dataset-variables").remove();
                if($("#uriVariable-selector :selected").length > 0){
                    $("#uriVariable-selector :selected").each(function (i, sel) {
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
                }else{
                    $('#dataset-csv-columns-desc').append(
                                '<tr class="dataset-variables">'
                              + '<th style="color:red">Variable value *</th>'
                              + '<td ><?= Yii::t('app', 'Variable value') ?> (<?= Yii::t('app', 'Real number, String or Date') ?>)'
                              + '</td>'
                              + '</tr>');
                }
            });

           

            // Initial document count
            var nbDocuments = -1;
            $(document).on('click', '#document-save', function () {
                // On save get document form values
                var formData = new FormData();
                var file_data = $('#document-content #yiidocumentmodel-file').prop('files')[0];
                formData.append('file', file_data);
                var other_data = $('form').serializeArray();
                $.each(other_data, function (key, input) {
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
            $('.buttonDocuments').click(function () {
                nbDocuments++;
                typeInsertedDocument = "document";
                $('#document-modal').modal('toggle');
            });

            // Download adjusted to variables CSV template file on click
            $(document).on('change', '#uriVariable-selector', function () {
                var variablesLabels = [];
                $("#uriVariable-selector :selected").each(function (i, sel) {
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
            
            populateVariableList($("#experiment-selector").val());
            
            // Verifiy saving form step
            $('.collapse').on('show.bs.collapse', function (e) {
                var target = e.target.id;
                var varStepString = "step-0" ;

                if( e.target.id === varStepString + 2 ){
                    if($("#experiment-selector :selected").val() === ""){
                        e.stopPropagation();
                        e.preventDefault();
                        toastr.warning('You must select an experiment, in step 1');
                    }
                }
                if( e.target.id === varStepString + 3 ){
                    if($("#experiment-selector :selected").val() === ""){
                        e.stopPropagation();
                        e.preventDefault();
                        toastr.warning('You must select an experiment, in step 1');
                    }else{
                        $("#" + varStepString + 2).collapse('hide');
                    }
                }
                if( e.target.id === varStepString + 4 ){
                    if($("#experiment-selector :selected").val() === ""){
                        e.stopPropagation();
                        e.preventDefault();
                        toastr.warning('You must select an experiment, in step 1');
                    }
                    if($("#provenance-selector :selected").val() === ""
                          ||  !provenances.hasOwnProperty($("#provenance-selector :selected").val())){
                        e.stopPropagation();
                        e.preventDefault();
                        toastr.warning('You must select a valid provenance or save your new provenance, in step 3');
                    }
                }
            });
        });
        
        /**
         * Fill variable dropdown
         * @param string experimentUri
         * @returns void        */
        function populateVariableList(experimentUri){
            if(experimentUri !== undefined && experimentUri !== null ){
                var select = $('#uriVariable-selector');
                var settings = select.attr('data-krajee-select2'),

                settings = window[settings];

                $.ajax({
                    url: '<?= Url::toRoute(['dataset/get-experiment-mesured-variables-select-list']); ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {"experimentUri": experimentUri}
                })
                .done(function (data) {

                    settings.data = data.data;
                    select.select2(settings);
                })
                .fail(function (jqXHR, textStatus) {
                    // Disaply errors
                    console.log(jqXHR)
                });
            }
        }
        
        /**
         * Save provenance model via ajax
         * @returns {Boolean}         */
        function saveProvenance(){

            var uriTest = $("#provenance-selector :selected").val();
            
            if(provenances.hasOwnProperty(uriTest)){
                  toastr.info("Already existing provenance");
            }
            
            if(!provenances.hasOwnProperty(uriTest) 
                    && $("#provenance-selector :selected").val() !== undefined
                    && $("#provenance-selector :selected").val() !== ""){
                var provenance = {};
                toastr.info("Saving provenance ...");
                provenance['label']= $("#provenance-selector :selected").val();
                provenance['comment']= $("#yiidatasetmodel-provenancecomment").val();
                provenance['documents']= [];
                if($("#yiidatasetmodel-documentsuris").val() !== undefined){
                    provenance['documents']= $("#yiidatasetmodel-documentsuris").val();
                }
                provenance['sensingDevices']= [];
                if($("#yiidatasetmodel-sensingDevices").val() !== undefined){
                    provenance['sensingDevices']= $("#yiidatasetmodel-sensingDevices").val();
                }
                provenance['agents']= [];
                if($("#yiidatasetmodel-provenanceagents").val() !== undefined){
                    provenance['agents']= $("#yiidatasetmodel-provenanceagents").val();
                }
                console.log(provenance);
                 $.ajax({
                    url: 'index.php?r=dataset%2Fcreate-provenance-from-dataset',
                    type: 'POST',
                    datatype: 'json',
                    data: provenance

                })
                .done(function (data) {
                    var select = $('#provenance-selector');
                    var settings = select.attr('data-krajee-select2'),
                    settings = window[settings];
                    
                    provenances = data.provenances;
                    console.log(provenances);
                    settings.data = data.provenancesByUri;
                    select.select2(settings);
                    updateProvenanceFields(data.newProvenanceUri);
                    console.log(provenances.hasOwnProperty(data.newProvenanceUri))
                    toastr.success("Provenance saved");
                })
                .fail(function (jqXHR, textStatus) {
                    // Disaply errors
                    $('#document-save-msg').parent().removeClass('alert-info');
                    $('#document-save-msg').parent().addClass('alert-danger');
                    $('#document-save-msg').html('Request failed: ' + textStatus);
                    toastr.error("An error has occured during provenance saving");
                });

            return false;   
            }
        }
        
        /**
        * Function to update provenance comment field depending of selected URI
         * @param string  provenance uri
         * @returns {undefined}         */
        function updateProvenanceFields(uri) {
            if (provenances.hasOwnProperty(uri)) {
                // If selected provenance is known get its comment
                var comment = provenances[uri]["comment"];

                // Set provenance comment, disable input and remove validation messages 
                $("#yiidatasetmodel-provenancecomment").val(comment).attr("disabled", "disabled");
                $(".field-yiidatasetmodel-provenancecomment, .field-yiidatasetmodel-provenancecomment *")
                        .removeClass("has-error")
                        .removeClass("has-success");
                $(".field-yiidatasetmodel-provenancecomment .help-block").empty();

                // Set provenance provenanceAgents, disable input and remove validation messages 
                try {
                    var provenanceAgents = provenances[uri]["metadata"]["prov:Agent"]["oeso:Operator"];
                    $("#yiidatasetmodel-provenanceagents").val(provenanceAgents);
                    $("#yiidatasetmodel-provenanceagents").trigger("change");
                } catch (error) {
                    $("#yiidatasetmodel-provenanceagents").val([]);
                    $("#yiidatasetmodel-provenanceagents").trigger("change");
                    console.log("No agents set");
                } finally {
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
                // Otherwise clear provenance agents and enable input
                $("#yiidatasetmodel-provenanceagents").val(agents).removeAttr("disabled").trigger("change");

                // Otherwise clear provenance comment and enable input
                $("#yiidatasetmodel-provenancecomment").val("").removeAttr("disabled");

                // Clear linked documents list
                $("#already-linked-documents").empty();
            }
        }
    </script>
</div>
</div>