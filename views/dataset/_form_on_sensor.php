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
use antkaz\vue\VueAsset;
VueAsset::register($this);
$this->registerJsFile("https://rawgit.com/cristijora/vue-form-wizard/master/dist/vue-form-wizard.js");
$this->registerCssFile("https://rawgit.com/cristijora/vue-form-wizard/master/dist/vue-form-wizard.min.css");
$this->registerCssFile("https://rawgit.com/lykmapipo/themify-icons/master/css/themify-icons.css");
?>
 <script>
            
        /**
         * Fill variable dropdown
         * @param string sensorUri
         * @returns void        */
        function populateVariableListFromSensorUris(sensorUris){
            var select = $('#uriVariable-selector');
            var settings = select.attr('data-krajee-select2'),
            settings = window[settings];
            if(sensorUris !== undefined && sensorUris !== null ){
                $.ajax({
                    url: '<?= Url::toRoute(['sensor/ajax-get-sensor-measured-variables-select-list']); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {"sensorUris": sensorUris}
                })
                .done(function (data) {
                    select.html("");
                    if(data.data.length === 0){
                        settings.data = {};
                        select.val('').change();
                    }else{
                        settings.data = data.data;
                        select.select2(settings).change();
                    }
                    
                    
                })
                .fail(function (jqXHR, textStatus) {
                    // Disaply errors
                    console.log(jqXHR)
                });
            }else{
                select.val('').change();
                select.html("");
            }
        }
        
        /**
         * Save provenance model via ajax
         * @returns {Boolean}         */
        function saveProvenance(){

            var newProvenanceUri = $("#provenance-selector :selected").val();
            
            if(provenances.hasOwnProperty(newProvenanceUri)){
                  toastr.info("Already existing provenance");
            }
            
            if(!provenances.hasOwnProperty(newProvenanceUri) 
                    && $("#provenance-selector :selected").val() !== undefined
                    && $("#provenance-selector :selected").val() !== ""){
                var provenance = {};
                toastr.info("Saving provenance ...");
                provenance['label']= $("#provenance-selector :selected").val();
                provenance['comment']= $("#yiidatasensormodel-provenancecomment").val();
                provenance['documents']= [];
                if($("#yiidatasensormodel-documentsuris").val() !== undefined){
                    provenance['documents']= $("#yiidatasensormodel-documentsuris").val();
                }
                provenance['sensingDevices']= [];
                if($("#yiidatasensormodel-provenancesensingdevices").val() !== undefined){
                    provenance['sensingDevices']= $("#yiidatasensormodel-provenancesensingdevices").val();
                }
                provenance['agents']= [];
                if($("#yiidatasensormodel-provenanceagents").val() !== undefined){
                    provenance['agents']= $("#yiidatasensormodel-provenanceagents").val();
                }
                var documents = [];
                var documentsInputList = $("input[id^='yiidatasensormodel-documentsuris-documenturi-']")
                documentsInputList.each(function (i, documentInput) {
                    documents.push($(documentInput).val());
                });
                
                $.ajax({
                    url: '<?= Url::toRoute(['provenance/ajax-create-provenance-from-dataset']); ?>',
                    type: 'POST',
                    datatype: 'json',
                    data: {provenance :provenance,
                            documents : documents}
                })
                .done(function (newProvenanceUri) {
                    if(newProvenanceUri !== false){
                        loadProvenances(newProvenanceUri)
                        .done(function (data) {
                            var select = $('#provenance-selector');
                            var settings = select.attr('data-krajee-select2'),
                            settings = window[settings];
                            provenances = data.provenances;
                            settings.data = data.provenancesByUri;
                            select.select2(settings);
                            updateProvenanceFields(newProvenanceUri);
                            if(newProvenanceUri !== undefined && newProvenanceUri !== null){
                                var $newOption = $("<option selected='selected'></option>").val(newProvenanceUri).text(data.provenancesByUri[newProvenanceUri])
                                select.append($newOption).trigger('change');
                            }
                            toastr.success("Provenance saved");
                        }).fail(function (jqXHR, textStatus) {
                                // Disaply errors
                                $('#document-save-msg').parent().removeClass('alert-info');
                                $('#document-save-msg').parent().addClass('alert-danger');
                                $('#document-save-msg').html('Request failed: ' + textStatus);
                                toastr.error("An error has occured during provenance saving");
                        });
                    }else{ 
                        toastr.error("An error has occured during provenance saving"); 
                    }
                   
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
        
        function updateProvenancesWithSpecificSensorUris(sensorUris){
            return $.ajax({
                    url: "<?= Url::toRoute(['provenance/ajax-get-specific-sensor-provenances-select-list']); ?>",
                    type: 'POST',
                    datatype: 'json',
                    data: {"sensorUris": sensorUris}
            }) .done(function (data) {
                console.log(data)
                var select = $('#provenance-selector');
                var settings = select.attr('data-krajee-select2'),
                settings = window[settings];
                select.html("");
                provenances = data.provenances;
                settings.data = data.provenancesByUri;
                select.select2(settings);
                select.val('').change()
            }).fail(function (jqXHR, textStatus) {
                    // Disaply errors
                    $('#document-save-msg').parent().removeClass('alert-info');
                    $('#document-save-msg').parent().addClass('alert-danger');
                    $('#document-save-msg').html('Request failed: ' + textStatus);
            });
        }
        
        function loadProvenances(provenanceSelectedUri){
            return $.ajax({
                    url: '<?= Url::toRoute(['provenance/ajax-get-provenances-select-list']); ?>',
                    type: 'POST',
                    datatype: 'json'
            });
        }
        
        /**
        * Function to update provenance comment field depending of selected URI
         * @param string  provenance uri
         * @returns {undefined}         */
        function updateProvenanceFields(uri) {
            console.log(provenances[uri])
            if (provenances.hasOwnProperty(uri)) {
                // If selected provenance is known get its comment
                var comment = provenances[uri]["comment"];

                // Set provenance comment, disable input and remove validation messages 
                $("#yiidatasensormodel-provenancecomment").val(comment).attr("disabled", "disabled");
                $(".field-yiidatasensormodel-provenancecomment, .field-yiidatasensormodel-provenancecomment *")
                        .removeClass("has-error")
                        .removeClass("has-success");
                $(".field-yiidatasensormodel-provenancecomment .help-block").empty();

                // Set provenance provenanceAgents, disable input and remove validation messages 
                try {
                    var provenanceAgents = provenances[uri]["metadata"]["prov:Agent"]["oeso:Operator"];
                    $("#yiidatasensormodel-provenanceagents").val(provenanceAgents);
                    $("#yiidatasensormodel-provenanceagents").trigger("change");
                } catch (error) {
                    $("#yiidatasensormodel-provenanceagents").val([]);
                    $("#yiidatasensormodel-provenanceagents").trigger("change");
                    console.log("No agents set");
                } finally {
                    $("#yiidatasensormodel-provenanceagents").val(agents).attr("disabled", "disabled");
                    $(".field-yiidatasensormodel-provenanceagents, .field-yiidatasensormodel-provenanceagents *")
                            .removeClass("has-error")
                            .removeClass("has-success");
                    $(".field-yiidatasensormodel-provenanceagents .help-block").empty();
                }
                
                // Load linked documents
                $("#already-linked-documents").load(documentsLoadUri, {
                    "uri": uri
                });
            } else {
                // Otherwise clear provenance agents and enable input
                $("#yiidatasensormodel-provenanceagents").val(agents).removeAttr("disabled").trigger("change");

                // Otherwise clear provenance comment and enable input
                $("#yiidatasensormodel-provenancecomment").val("").removeAttr("disabled");

                // Clear linked documents list
                $("#already-linked-documents").empty();
            }
        }
        
        /**
         *
         * @returns void         */
        function refreshRules(){
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
        }
        
        function saveDocument(){
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
                    $('#yiidatasensormodel-documentsuris-documenturi-' + nbDocuments).val(data);
                    documentUploaded = true;
                    $('#document-modal').modal('toggle');

            })
            .fail(function (jqXHR, textStatus) {
                    // Disaply errors
                    $('#document-save-msg').parent().removeClass('alert-info');
                    $('#document-save-msg').parent().addClass('alert-danger');
                    $('#document-save-msg').html('Request failed: ' + textStatus);
            });
        }
    </script>
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
        echo 'provenances =  {};';
        // Inject sensingDevices list indexed by URI
        echo 'sensingDevices = ' . json_encode($this->params['sensingDevices']) . ';';
        // Inject agents list indexed by URI
        echo 'agents = ' . json_encode($this->params['agents']) . ';';
        ?>

    });
    </script>
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
        <div id="app">
            <div>
                <template>
                <form-wizard @on-complete="onComplete"
                             @on-validate="handleValidation"
                             @on-loading="setLoading"
                              shape="square"
                              color="#3498db"
                              back-button-text="<?= Yii::t('app','Back') ?>"
                              next-button-text="<?= Yii::t('app','Next') ?>"
                              finish-button-text="<?= Yii::t('app', 'Upload dataset') ?>"
                              >
                    <div class="loader" v-if="loadingWizard"></div>
                    <tab-content title="<?= Yii::t('app','Choose a sensor') ?>"
                                 icon="ti-user" 
                                 :before-change="beforeSelectProvenance">
                        
        <?= $form->field($model, 'provenanceSensingDevices')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['sensingDevices'],
            'id' => 'sensor-selector',
            'options' => [
                'placeholder' => Yii::t('app/messages', 'Select existing device') . ' ...',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => false,
                'tags' => true,
                'maximumSelectionLength' =>'1'
            ],
                'pluginEvents' => [
                'select2:select' => 'function(e) { populateVariableListFromSensorUris(e.params.data.id); }',
            ]
        ]); ?>
            </tab-content>
            <tab-content title="<?= Yii::t('app','Generate dataset template (Optional)') ?>"
                                         icon="ti-user" 
                                         :before-change="beforeSelectProvenance">
            <hr style="border-color:gray;"/>
            <h3><i>  <?= Yii::t('app', 'Need a dataset template ? (Optional)') ?></i></h3>
            <p class="alert alert-info"><?= Yii::t('app/messages', 'The variables associated to the choosen experiment'); ?></p>
        <?php
            $select2VariablesOptions =  [
                'placeholder' => Yii::t('app/messages', 'Select one or more variables') . ' ...',
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
                    'tags' => false
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
    
                </i>
                </p>

                <hr>
                <i style="float: right"><?= Html::a("<span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> " . Yii::t('app', 'Download Example'), \config::path()['basePath'] . 'documents/DatasetFiles/' . $csvPath . '/datasetSensorExemple.csv') ?></i>
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
                                <th style="color:red">Date *</th>
                                <td><p><?= Yii::t('app/messages', 'Acquisition date of the data') ?> (format ISO 8601: YYYY-MM-DD or YYYY-MM-DDTHH:mm:ssZ) </p> </td>
                            </tr>
                            <tr class="dataset-variables">
                                <th style="color:red">Variable value *</th>
                                <td ><?= Yii::t('app', 'Variable value') ?> (<?= Yii::t('app', 'Real number, String or Date') ?>)</td>
                            </tr>
                        </table>
                    </div>               
                    
        <?= 
        Html::a("<button type='button' class='btn btn-success'> " . Yii::t('app', 'Download Template') . "</button>",
            \config::path()['basePath'] . 'documents/DatasetFiles/' . $csvPath . '/datasetSensorTemplate.csv',
            ['id' => 'downloadDatasetTemplate']
        );
        ?>
                <br><br>
                </tab-content>
                <tab-content title="<?= Yii::t('app','Describe the produced dataset') ?>"
                                 icon="ti-settings"
                                 :before-change="beforeUploadDataset">
                       <h4><?= Yii::t('app', 'Provenance'); ?></h4>
                <p class="alert alert-info"><?= Yii::t('app/messages', 'To create a new provenance, write the provenance label in the research field and press `Enter`'); ?></p>
    
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
                'label' => Yii::t('app', 'Add Document'),            
            ],
        ])->label(false)
        ?>
        <br>
        <?= Html::button(Yii::t('app', 'Create provenance'), ['class' => 'btn btn-success','onclick' => 'saveProvenance()']) ?>
        <br>
        <br>

                </tab-content>
                <tab-content title="<?= Yii::t('app','Upload dataset'); ?>"
                             icon="ti-check">
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
                        </div>
                        </div>
        <?php ActiveForm::end(); ?>
                    </tab-content>
                </form-wizard>
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
            </template>
            </div>
        </div>
    </div>
    <div>
    <script>
        $(document).ready(function () {
            Vue.use(VueFormWizard)
            new Vue({
             el: '#app',
             data:{
                loadingWizard: false
             },
             methods: {
                onComplete: function(){
                    $("#<?= $form->getId() ?>").submit();
                },
                setLoading: function(value) {
                      this.loadingWizard = value
                },
                handleValidation: function(isValid, tabIndex){
                      console.log('Tab: '+tabIndex+ ' valid: '+isValid)
                },
                beforeSelectProvenance: function(){
                      return new Promise((resolve, reject) => {
                          setTimeout(() => {
                              let experimentUri = $("#yiidatasensormodel-provenancesensingdevices").val();
                              if(experimentUri === undefined || experimentUri === null || experimentUri === "" ){
                                  toastr.warning("You must select a valid experiment to continued");
                                  reject("You must select a valid experiment to continue");
                              }else{
                                 resolve(true);
                                }   
                              }, 200)
                      });
                  },
                  beforeUploadDataset: function(){
                      return new Promise((resolve, reject) => {
                          setTimeout(() => {
                              let provenanceUri = $("#provenance-selector").val();
                              if(provenanceUri === undefined ||
                                      provenanceUri === null ||
                                      provenanceUri === "" ||
                                      !provenances.hasOwnProperty(provenanceUri)){
                                  toastr.warning("You must select a valid provenance to continued");
                                  reject("You must select a valid provenance to continue");
                              }else{
                                 resolve(true);
                                }   
                              }, 200)
                      });
                  }
              }
        });
            
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
           refreshRules();
        });

        // Initial document count
        var nbDocuments = -1;
        $(document).on('click', '#document-save', function () {
            saveDocument();
            return false;
        });

        // Open document add form on click
        $('.buttonDocuments').click(function () {
            console.log("click")
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
                url: 'index.php?r=dataset%2Fgenerate-and-download-sensor-dataset-creation-file',
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
        
        // On provenance change update provenance fields
        $("#yiidatasensormodel-provenancesensingdevices").change(function () {
             populateVariableListFromSensorUris($(this).val());
             updateProvenancesWithSpecificSensorUris($(this).val());
        });
        
        populateVariableListFromSensorUris($("#yiidatasensormodel-provenancesensingdevices").val());
        
        // On provenance change update provenance fields
        $("#provenance-selector").change(function () {
            updateProvenanceFields($(this).val());
        });

        // Update provenance fields depending of startup value
        updateProvenanceFields($("#provenance-selector").val());
     });    
    </script>
    <style>
        /* This is a css loader. It's not related to vue-form-wizard */
        .loader,
        .loader:after {
          border-radius: 50%;
          width: 10em;
          height: 10em;
        }
        .loader {
          margin: 60px auto;
          font-size: 10px;
          position: relative;
          text-indent: -9999em;
          border-top: 1.1em solid rgba(255, 255, 255, 0.2);
          border-right: 1.1em solid rgba(255, 255, 255, 0.2);
          border-bottom: 1.1em solid rgba(255, 255, 255, 0.2);
          border-left: 1.1em solid #0033ff;
          -webkit-transform: translateZ(0);
          -ms-transform: translateZ(0);
          transform: translateZ(0);
          -webkit-animation: load8 1.1s infinite linear;
          animation: load8 1.1s infinite linear;
        }
        @-webkit-keyframes load8 {
          0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
          }
        }
        @keyframes load8 {
          0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
          }
        }
    </style>
</div>
</div>