<?php

//******************************************************************************
//                                       _form.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 mars 2018
// Subject: creation of sensors by handsontable
//******************************************************************************
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>

<div class="dataset-form">
    <div id="sensors-created" class="alert alert-success">Sensors Created</div>
    <!--<button type = "button" id="export" id="exportButton">Export</button>-->
    <div id="dataset-multiple-insert-table"></div>
    <div id="dataset-multiple-insert-button" style="margin-top : 1%">
        <button type="button" class="btn btn-success" id="sensors-save"><?= Yii::t('app', 'Create Sensors') ?></button>
    </div>
    <script>
        var sensingDevicesTypes = JSON.parse('<?php echo $sensorsTypes; ?>');
        
        $('#sensors-created').hide();
           
        // Empty validator
        emptyValidator = function(value, callback) {
          if (!value || 0 === value.length) {
            callback(false);
          } else {
            callback(true);
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
                    source: sensingDevicesTypes,
                    strict: true,
                    required: true,
//                    validator: emptyValidator
                },
                {
                    data: 'brand',
                    type: 'text',
                    required: true,
                    validator: emptyValidator
                },
                {
                    data: 'inServiceDate',
                    type: 'date',
                    dateFormat: 'YYYY-MM-DD',
                    required: false
                },
                {
                    data: 'dateOfPurchase',
                    type: 'date',
                    dateFormat: 'YYYY-MM-DD',
                    required: false
                },
                {
                    data: 'dateOfLastCalibration',
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
                '<b><?= Yii::t('app', 'In Service Date') ?></b>',
                '<b><?= Yii::t('app', 'Date Of Purchase') ?></b>',
                '<b><?= Yii::t('app', 'Date Of Last Calibration') ?></b>'
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
                sensors = handsontable.getData();
                $.ajax({
                    url: 'index.php?r=sensor%2Fcreate-multiple-sensors',
                    type: 'POST',
                    dataType: 'json',
                    data: {sensors: sensors}
                }).done(function (data) {
                    for (var i = 0; i < data.length; i++) {
                       handsontable.setDataAtCell(0, i, data[i]);
                    }
                    
                    $('#sensors-save').hide();
                    $('#sensors-created').show();
                })
                .fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                });
            } 
        } 
         
         //save sensors
         $(document).on('click', '#sensors-save', function() {
             handsontable.validateCells(add);
         });
         
    </script>
</div>