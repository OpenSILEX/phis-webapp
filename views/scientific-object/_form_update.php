<?php

//******************************************************************************
//                                       _form_update.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 11 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
?>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>

<div class="scientificObject-form-update">
    <p><i>See the <a href="https://opensilex.github.io/phis-docs-community/experimental-organization/#importing-scientific-objects" target="_blank">documentation</a> to get more informations about the columns contents.</i></p>
    <!--<button type = "button" id="export" id="exportButton">Export</button>-->
    <div class="alert alert-info" role="alert">
        <table class="table table-hover">
            <tr>
                <td><p><?= Yii::t('app/messages', 'To change the value of one property (e.g. modality), you need to provide all of its former properties that you want to keep, otherwise this information will be lost.')?></p></td>
            </tr>
            <tr>
                <td><p><?= Yii::t('app/messages', 'It is not possible to change the experiment.')?></p></td>
            </tr>
            <tr>
                <td><p><?= Yii::t('app/messages', 'To assign scientific objects to two experiments, you need to first create the scientific objects in one of the experiments. Then, update the scientific objects with the uri of the second experiment in the "experiment" column and their metadata in the second experiment (e.g. give the new alias and repeat the experimental modality values if they are the same).') ?></p></td>
            </tr>
        </table>
    </div>
    <div id="objects-to-update">
        <div id="object-multiple-update-table"></div>
        <div id="object-multiple-update-button" style="margin-top : 1%">
            <button type="button" class="btn btn-primary" id="objects-update"><?= Yii::t('app', 'Update Scientific Objects') ?></button>
        </div>
    </div>
    <div id="loader" class="loader" style="display:none"></div>
    
    <script>
        var objectsTypes = JSON.parse('<?php echo $objectsTypes; ?>');
        var experiments = JSON.parse('<?php echo $experiments; ?>');
        var species = JSON.parse('<?php echo $species; ?>');
        
        $('#objects-updated').hide();
        
        // Empty validator
        var emptyValidator = function(value, callback) {
          if (isEmpty(value)) {
            callback(false);
          } else {
            callback(true);
          }
        };
        
        /**
         * 
         * @param {type} value
         * @returns {Boolean} true if value is empty
         */
        function isEmpty(value) {
          if (!value || 0 === value.length) {
            return true;
          } else {
            return false;
          }
        }
        
        /**
         * validate an object type value. callback will be true if the value is 
         * not empty and is a object type
         * @param {type} value
         * @param {type} callback
         * @returns {undefined} 
         */
        var objectTypeValidator = function(value, callback) {
            if (isEmpty(value)) {
                callback(false);
            } else if (objectsTypes.indexOf(value) > -1) {
                callback(true);
            } else {
                callback(false);
            }
        };
        
        /**
         * validate an experiment cell value. callback will be true if the value is 
         * not empty and is an experiment from the experiments list
         * @param {type} value
         * @param {type} callback
         * @returns {undefined} 
         */
        var experimentValidator = function(value, callback) {
            if (isEmpty(value)) {
                callback(false);
            } else if (experiments.indexOf(value) > -1) {
                callback(true);
            } else {
                callback(false);
            }
        };
        
        var speciesValidator = function(value, callback) {
          if (isEmpty(value)) {
                callback(true);
            } else if (species.indexOf(value) > -1) {
                callback(true);
            } else {
                callback(false);
            }  
        };
        
        //creates renderer to color in red required column names
        function firstRowRequiedRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);
            td.style.color = 'red';
            td.style.fontWeight = 'bold';
        }   
        
        //creates renderer for the read only columns
        function readOnlyColumnRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);
            td.style.fontWeight = 'bold';
            td.style.background = '#EEE';
        }
           
           
        //generate handsontable
         var hotElement = document.querySelector('#object-multiple-update-table');        
         var handsontable = new Handsontable(hotElement, {
            startRows: 1,
            columns: [
                {
                    data: 'uri',
                    type: 'text',
                    required: true,
                    validator: emptyValidator
                },
                {
                    data: 'alias',
                    type: 'text',
                    required: true,
                    validator: emptyValidator
                },
                {
                    data: 'type',
                    type: 'dropdown',
                    source: objectsTypes,
                    strict: true,
                    required: true,
                    validator: objectTypeValidator
                },
                {
                    data: 'experiment',
                    type: 'dropdown',
                    source: experiments,
                    strict: true,
                    required: true,
                    validator: experimentValidator
                },
                {
                    data: 'geometry',
                    type: 'text',
                    required: false
                },
                {
                    data: 'parent',
                    type: 'text',
                    required: false
                },
                {
                    data: 'species',
                    type: 'dropdown',
                    source: species,
                    required: false,
                    validator: speciesValidator
                },
                {
                    data: 'variety',
                    type: 'text',
                    required: false
                },
                {
                    data: 'modalities',
                    type: 'text',
                    required: false
                },
                {
                    data: 'replication',
                    type: 'text',
                    required: false
                },
                {
                    data: 'insertion status',
                    type: 'text',
                    required: false,
                    readOnly: true
                }

            ],
            rowHeaders: true,
            colHeaders: [
                "<b><?= Yii::t('app', 'URI') ?></b>",
                "<b><?= Yii::t('app', 'Alias') ?></b>",
                "<b><?= Yii::t('app', 'Type') ?></b>",
                "<b><?= Yii::t('app', 'Experiment') ?></b>",
                "<b><?= Yii::t('app', 'Geometry') ?></b>",
                "<b><?= Yii::t('app', 'Parent') ?></b>",
                "<b><?= Yii::t('app', 'Species') ?></b>",
                "<b><?= Yii::t('app', 'Variety') ?></b>",
                "<b><?= Yii::t('app', 'Experiment Modalities') ?></b>",
                "<b><?= Yii::t('app', 'Replication') ?></b>",
                "<b><?= Yii::t('app', 'Insertion status') ?></b>"
            ],
            manualRowMove: true,
            manualColumnMove: true,
            contextMenu: true,
            filters: true,
            dropdownMenu: true,
            cells: function(row, col, prop) {
                var cellProperties = {};
                
                if (col === 10) {
                    cellProperties.renderer = readOnlyColumnRenderer;
                }
                
                return cellProperties;
            },
            afterGetColHeader: function (col, th) {
                if (col === 0 | col === 1 | col === 2 | col === 3) {
                    th.style.color = "red";
                }
            }
            
         });
         
        /**
         * if the data is valid, calls the update action
         * @param {boolean} callback
         * @returns 
         */
        function update(callback) {
            if (callback) {
                document.getElementById("loader").style.display = "block";
                document.getElementById("objects-to-update").style.display = "none";
                
                var objectsArray = handsontable.getData();
                var objectsString = JSON.stringify(objectsArray);
                $.ajax({
                    url: 'index.php?r=scientific-object%2Fupdate-multiple-scientific-objects',
                    type: 'POST',
                    dataType: 'json',
                    data: {objects: objectsString}
                }).done(function (data) {
                    document.getElementById("objects-to-update").style.display = "block";
                    document.getElementById("loader").style.display = "none";
                    for (var i = 0; i < data["messages"].length; i++) {
                        if (data["objectUris"][i] !== null) {
                            handsontable.setDataAtCell(i, 0, data["objectUris"][i]);
                        }
                        handsontable.setDataAtCell(i, 10, data["messages"][i]);                        
                    }                   
                    $('#objects-save').hide();
                    if (data["error"]) {
                        toastr["error"]("<?= Yii::t('app/messages', 'The object update has failed. See insertion status column for more details.') ?>");
                    } else {
                        toastr["success"]("<?= Yii::t('app/messages', 'Objects successfully updated') ?>");
                    }
                })
                .fail(function (jqXHR, textStatus) {
                    console.log(textStatus);
            console.log("jqXHR");
                    console.log(jqXHR);
                    toastr["error"]("The object update has failed");
                    document.getElementById("objects-to-update").style.display = "block";
                    document.getElementById("loader").style.display = "none";
                });
            } 
        }
         
         //save objects
         $(document).on('click', '#objects-update', function() {
             handsontable.validateCells(update);
         });
    </script>
</div>