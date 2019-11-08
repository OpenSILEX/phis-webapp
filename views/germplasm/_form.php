<?php

//**********************************************************************************************
//                                       _form.php
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 31 2017
// Subject: creation of scientific objects via CSV
//***********************************************************************************************

use yii\widgets\ActiveForm;
use kartik\select2\Select2;

require_once '../config/config.php';

/* @var $this yii\web\View */
/* @var $model app\models\YiiGermplasmModel */
/* @var $form yii\widgets\ActiveForm */

?>


<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>

<div class="germplasm-form well">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <?= $form->field($model, 'germplasmType')->widget(Select2::classname(), [
            'data' => $this->params['listGermplasmTypes'],
            'options' => ['placeholder' => 'Select germplasm type',
                                'multiple' => false],
        ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('yii' , 'Create') , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <p><i>
        <?= Yii::t('app', 'See') ?>
        <a href="https://opensilex.github.io/phis-docs-community/experimental-organization/#importing-scientific-objects" target="_blank"><?= Yii::t('app', 'the documentation') ?></a>
        <?= Yii::t('app/messages', 'to get more information about the columns content') ?>.
    </i></p>  
    
    <div id="germplasms-created" class="alert alert-success"><?= Yii::t('app', 'Germplasms Created') ?></div>
    <!--<button type = "button" id="export" id="exportButton">Export</button>-->
    <div id="germplasms-creation">
        <div id="germplasms-multiple-insert-table"></div>
        <div id="germplasms-multiple-insert-button" style="margin-top : 1%">
            <button type="button" class="btn btn-success" id="germplasms-save"><?= Yii::t('app', 'Create Germplasms') ?></button>
        </div>
    </div>
    <div id="loader" class="loader" style="display:none"></div>
    <script>
//        var objectsTypes = JSON.parse('<?php echo addslashes($objectsTypes); ?>');
//        var experiments = JSON.parse('<?php echo addslashes($experiments); ?>');
//        var species = JSON.parse('<?php echo addslashes($species); ?>');

        $('#germplasms-created').hide();

//        // Empty validator
//        var emptyValidator = function(value, callback) {
//          if (isEmpty(value)) {
//            callback(false);
//          } else {
//            callback(true);
//          }
//        };
//
//        /**
//         *
//         * @param {type} value
//         * @returns {Boolean} true if value is empty
//         */
//        function isEmpty(value) {
//          if (!value || 0 === value.length) {
//            return true;
//          } else {
//            return false;
//          }
//        }
//
//        /**
//         * validate an object type value. callback will be true if the value is
//         * not empty and is a object type
//         * @param {type} value
//         * @param {type} callback
//         * @returns {undefined}
//         */
//        var objectTypeValidator = function(value, callback) {
//            if (isEmpty(value)) {
//                callback(false);
//            } else if (objectsTypes.indexOf(value) > -1) {
//                callback(true);
//            } else {
//                callback(false);
//            }
//        };



//        var speciesValidator = function(value, callback) {
//          if (isEmpty(value)) {
//                callback(true);
//            } else if (species.indexOf(value) > -1) {
//                callback(true);
//            } else {
//                callback(false);
//            }
//        };

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
         var hotElement = document.querySelector('#germplasms-multiple-insert-table');
         var handsontable = new Handsontable(hotElement, {
            startRows: 1,
            columns: [
                {
                    data: 'genus',
                    type: 'text',
                    required: false,
                    readOnly: true
                },
                {
                    data: 'species',
                    type: 'text',
                    //source: species,
                    required: false
                    //validator: speciesValidator
                },
                {
                    data: 'variety',
                    type: 'text',
                    required: false
                },
                                {
                    data: 'accession',
                    type: 'text',
                    required: false
                },
                                {
                    data: 'lotType',
                    type: 'text',
                    required: false
                },
                                {
                    data: 'lot',
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
                "<b><?= Yii::t('app', 'Genus') ?></b>",
                "<b><?= Yii::t('app', 'Species') ?></b>",
                "<b><?= Yii::t('app', 'Variety') ?></b>",
                "<b><?= Yii::t('app', 'Accession') ?></b>",
                "<b><?= Yii::t('app', 'LotType') ?></b>",
                "<b><?= Yii::t('app', 'Lot') ?></b>",

                "<b><?= Yii::t('app', 'Insertion status') ?></b>"
            ],
            manualRowMove: true,
            manualColumnMove: true,
            contextMenu: true,
            filters: true,
            dropdownMenu: true,
            cells: function(row, col, prop) {
                var cellProperties = {};

                if (col === 0 || col === 10) {
                    cellProperties.renderer = readOnlyColumnRenderer;
                }

                return cellProperties;
            },
            afterGetColHeader: function (col, th) {
                if (col === 1 | col === 2 | col === 3 ) {
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
                document.getElementById("germplasms-creation").style.display = "none";

                var objectsArray = handsontable.getData();
                var objectsString = JSON.stringify(objectsArray);
                $.ajax({
                    url: 'index.php?r=germplasm%2Fcreate',
                    type: 'POST',
                    dataType: 'json',
                    data: {objects: objectsString}
                }).done(function (data) {
                    document.getElementById("germplasms-creation").style.display = "block";
                    document.getElementById("loader").style.display = "none";
                    for (var i = 0; i < data["messages"].length; i++) {
                        if (data["objectUris"][i] !== null) {
                            handsontable.setDataAtCell(i, 0, data["objectUris"][i]);
                        }
                        handsontable.setDataAtCell(i, 10, data["messages"][i]);
                    }
                    $('#germplasms-save').hide();
                })
                .fail(function (jqXHR, textStatus) {
                    toastr["error"]("The germplasms creation has failed");
                    document.getElementById("germplasms-creation").style.display = "block";
                    document.getElementById("loader").style.display = "none";
                });
            }
        }

         //save objects
         $(document).on('click', '#germplasms-save', function() {
             handsontable.validateCells(add);
         });
    </script>
    
</div>
