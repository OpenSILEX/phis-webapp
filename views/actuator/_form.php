<?php

//******************************************************************************
//                                       _form.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>

<div class="dataset-form well">
    <div id="actuators-created" class="alert alert-success"><?= Yii::t('app', 'Actuators Created') ?></div>
    <div id="actuators-creation">
        <div id="dataset-multiple-insert-table"></div>
        <div id="dataset-multiple-insert-button" style="margin-top : 1%">
            <button type="button" class="btn btn-success" id="actuators-save"><?= Yii::t('app', 'Create Actuators') ?></button>
        </div>
    </div>
    <div id="loader" class="loader" style="display:none"></div>
    <script>  
        var actuatorsTypes = JSON.parse('<?php echo $actuatorsTypes; ?>');
        var users = JSON.parse('<?php echo $users; ?>');
        
        $('#actuators-created').hide();
           
        // Empty validator
        emptyValidator = function(value, callback) {
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
         * validate a actuator value. callback will be true if the value is 
         * not empty and is a actuator type
         * @param {type} value
         * @param {type} callback
         * @returns {undefined} 
         */
        actuatorTypeValidator = function(value, callback) {
            if (isEmpty(value)) {
                callback(false);
            } else if (actuatorsTypes.indexOf(value) > -1) {
                callback(true);
            } else {
                callback(false);
            }
        };
        
        /**
         * validate a person in charge value (must be an email of the users list)
         * will be true if the value is not empty and is part of the users list
         * @param {type} value
         * @param {type} callback
         * @returns {undefined}
         */
        personInChargeValidator = function(value, callback) {
          if (isEmpty(value)) {
                callback(false);
            } else if (users.indexOf(value) > -1) {
                callback(true);
            } else {
                callback(false);
            }  
        };
           
        function firstRowRequiedRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);
            td.style.color = 'red';
            td.style.fontWeight = 'bold';
        }   
        
        function uriColumnRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);
            td.style.fontWeight = 'bold';
            td.style.background = '#EEE';
        }
           
           
        //generate handsontable
         var hotElement = document.querySelector('#dataset-multiple-insert-table');        
         var handsontable = new Handsontable(hotElement, {
            startRows: 2,
            columns: [
                {
                    data: 'uri',
                    type: 'text',
                    required: false,
                    readOnly: true
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
                    source: actuatorsTypes,
                    strict: true,
                    required: true,
                    validator: actuatorTypeValidator
                },
                {
                    data: 'brand',
                    type: 'text',
                    required: true,
                    validator: emptyValidator
                },
                {
                    data: 'serialNumber',
                    type: 'text',
                    required: false
                },
                {
                    data: 'model',
                    type: 'text',
                    required: false
                },
                {
                    data: 'dateOfPurchase',
                    type: 'date',
                    dateFormat: 'YYYY-MM-DD',
                    required: false
                },
                {
                    data: 'inServiceDate',
                    type: 'date',
                    dateFormat: 'YYYY-MM-DD',
                    required: false
                },
                {
                    data: 'dateOfLastCalibration',
                    type: 'date',
                    dateFormat: 'YYYY-MM-DD',
                    required: false
                },
                { 
                    data: 'personInCharge',
                    type: 'dropdown',
                    source: users,
                    strict: true,
                    required: true,
                    validator: personInChargeValidator
                }
            ],
            rowHeaders: true,
            colHeaders: [
                "<b><?= Yii::t('app', 'Generated URI') ?></b>",
                "<b><?= Yii::t('app', 'Alias') ?></b>",
                "<b><?= Yii::t('app', 'Type') ?></b>",
                "<b><?= Yii::t('app', 'Brand') ?></b>",
                "<b><?= Yii::t('app', 'Serial Number') ?></b>",
                "<b><?= Yii::t('app', 'Model') ?></b>",
                "<b><?= Yii::t('app', 'Date Of Purchase') ?></b>",
                "<b><?= Yii::t('app', 'In Service Date') ?></b>",
                "<b><?= Yii::t('app', 'Date Of Last Calibration') ?></b>",
                "<b><?= Yii::t('app', 'Person In Charge') ?></b>" 
            ],
            manualRowMove: true,
            manualColumnMove: true,
            contextMenu: true,
            filters: true,
            dropdownMenu: true,
            cells: function(row, col, prop) {
                var cellProperties = {};
                
                if (col === 0) {
                    cellProperties.renderer = uriColumnRenderer;
                }
                
                return cellProperties;
            },
            afterGetColHeader: function (col, th) {
                if (col === 1 | col === 2 | col === 3 | col === 9) {
                    th.style.color = "red";
                }
            }
         });
         
        /**
         * if the data is valid, calls the insert action
         * @param {boolean} callback
         * @returns 
         */
        function add(callback) {
            if (callback) {
                document.getElementById("loader").style.display = "block";
                document.getElementById("actuators-creation").style.display = "none";
                
                actuatorsArray = handsontable.getData();
                actuatorsString = JSON.stringify(actuatorsArray);
                
                $.ajax({
                    url: 'index.php?r=actuator%2Fcreate-multiple-actuators',
                    type: 'POST',
                    dataType: 'json',
                    data: {actuators: actuatorsString}
                }).done(function (data) {
                    document.getElementById("actuators-creation").style.display = "block";
                    document.getElementById("loader").style.display = "none";
                    for (var i = 0; i < data.length; i++) {
                       handsontable.setDataAtCell(i, 0, data[i]);
                    }
                    
                    $('#actuators-save').hide();
                    $('#actuators-created').show();
                })
                .fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                    document.getElementById("actuators-creation").style.display = "block";
                    document.getElementById("loader").style.display = "none";
                });
            } 
        } 
         
         //save actuators
         $(document).on('click', '#actuators-save', function() {
             handsontable.validateCells(add);
         });
         
    </script>
</div>
