<?php

//******************************************************************************
//                                       _form.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject: creation of vectors by handsontable
//******************************************************************************
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>

<div class="dataset-form">
    <div id="vectors-created" class="alert alert-success">Vectors Created</div>
    <!--<button type = "button" id="export" id="exportButton">Export</button>-->
    <div id="vector-multiple-insert-table"></div>
    <div id="vector-multiple-insert-button" style="margin-top : 1%">
        <button type="button" class="btn btn-success" id="vectors-save"><?= Yii::t('app', 'Create Vectors') ?></button>
    </div>
    <script>
        var vectorsTypes = JSON.parse('<?php echo $vectorsTypes; ?>');
        console.log(vectorsTypes);
        
        $('#vectors-created').hide();
           
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
         * validate a vector type value. callback will be true if the value is 
         * not empty and is a vector type
         * @param {type} value
         * @param {type} callback
         * @returns {undefined} 
         */
        vectorTypeValidator = function(value, callback) {
            if (isEmpty(value)) {
                callback(false);
            } else if (vectorsTypes.indexOf(value) > -1) {
                callback(true);
            } else {
                callback(false);
            }
        }
           
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
         var hotElement = document.querySelector('#vector-multiple-insert-table');        
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
                    source: vectorsTypes,
                    strict: true,
                    required: true,
                    validator: vectorTypeValidator
                },
                {
                    data: 'brand',
                    type: 'text',
                    required: true,
                    validator: emptyValidator
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
                }
            ],
            rowHeaders: true,
            colHeaders: [
                '<b><?= Yii::t('app', 'Generated URI') ?></b>',
                '<b><?= Yii::t('app', 'Alias') ?></b>',
                '<b><?= Yii::t('app', 'Type') ?></b>',
                '<b><?= Yii::t('app', 'Brand') ?></b>',
                '<b><?= Yii::t('app', 'Date Of Purchase') ?></b>',
                '<b><?= Yii::t('app', 'In Service Date') ?></b>'
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
                if (col === 1 | col === 2 | col === 3) {
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
                vectorsArray = handsontable.getData();
                vectorsString = JSON.stringify(vectorsArray);
                $.ajax({
                    url: 'index.php?r=vector%2Fcreate-multiple-vectors',
                    type: 'POST',
                    dataType: 'json',
                    data: {vectors: vectorsString}
                }).done(function (data) {
                    for (var i = 0; i < data.length; i++) {
                       handsontable.setDataAtCell(i, 0, data[i]);
                    }
                    
                    $('#vectors-save').hide();
                    $('#vectors-created').show();
                })
                .fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                });
            } 
        } 
         
         //save sensors
         $(document).on('click', '#vectors-save', function() {
             handsontable.validateCells(add);
         });
         
    </script>
</div>