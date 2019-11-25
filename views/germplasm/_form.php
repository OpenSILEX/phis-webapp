<?php

//**********************************************************************************************
//                                       _form.php
//
// Author(s): Alice BOIZET
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November 2019
// Contact: alice.boizet@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 08 2019
// Subject: creation of germplasms with handsontable
//***********************************************************************************************

use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;

require_once '../config/config.php';

/* @var $this yii\web\View */
/* @var $model app\models\YiiGermplasmModel */
/* @var $form yii\widgets\ActiveForm */

?>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>
<script src="https://unpkg.com/papaparse@latest/papaparse.min.js"></script>

<div class="well row">
    
    <div id="germplasm-selection" class="form-group col-md-6">
    <?php $form = ActiveForm::begin(); ?>   
        <div class="form-row">
            <label class="control-label" ><?= Yii::t('app', 'Select germplasm Type') ?></label>
            <?php
            echo Select2::widget([
                'name' => 'germplasmType',
                'data' => $germplasmTypes,
                'value' => $selectedGermplasmType ? $selectedGermplasmType : null,
                'options' => [
                    'placeholder' => Yii::t('app/messages', 'Select the type of Germplasm...'),
                    'multiple' => false
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents' => [
                    "select2:select" => "function() {  $('#germplasmType-creation-selection').text(\"" . Yii::t('app', 'Validate') . "\"); }",
                ]
            ]);
            ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('yii' , 'Validate') , ['class' => 'btn btn-success', 'id' => 'germplasmType-creation-selection']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php 
        $selectedGermplasmType = $_POST['germplasmType'];
        //echo $selectedGermplasmType;
        ?>        
    </div>        
    <div class="form-group col-md-6">
        <div id="import-csv"class="alert alert-info" role="alert">
            <b><?= Yii::t('app/messages', 'You can import directly a csv file to fill the table')?> : </b>
            <ul>
                <li><?= Yii::t('app/messages', 'CSV separator must be')?> "<b><?= Yii::$app->params['csvSeparator']?></b>"</li>              
            </ul>
            <input type="file" id="files"  class="form-control" accept=".csv" required onchange="fillTheTable(this.files)"/>

        </div>    
    </div>         
    
    
    <div id="germplasm-table">
        <div id="documentation-info">
            <p><i>
                <?= Yii::t('app', 'See') ?>
                <a href="https://opensilex.github.io/phis-docs-community/" target="_blank"><?= Yii::t('app', 'the documentation') ?></a>
                <?= Yii::t('app/messages', 'to get more information about the columns content') ?>.
            </i></p>  
        </div>

        <div id="germplasms-creation">
            <div id="germplasms-multiple-insert-table"></div>
            <div id="germplasms-multiple-insert-button" style="margin-top : 1%">
                <button type="button" class="btn btn-success" id="germplasms-save"><?= Yii::t('app', 'Create Germplasms') ?></button>
            </div>
        </div>
        <div id="loader" class="loader" style="display:none"></div>
        <script>
            var genusList = JSON.parse('<?php echo addslashes($genusList); ?>');
            var speciesList = JSON.parse('<?php echo addslashes($speciesList); ?>');
            var varietiesList = JSON.parse('<?php echo addslashes($varietiesList); ?>');
            var accessionsList = JSON.parse('<?php echo addslashes($accessionsList); ?>');
            var lotTypesList = JSON.parse('<?php echo addslashes($lotTypesList); ?>');

            $('#germplasms-multiple-insert-button').hide();
            $('#import-csv').hide();
            $('#documentation-info').hide();

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


            //Generate handsontable    
            var germplasmType = '<?php echo $selectedGermplasmType; ?>';

            var uriColumn = {
                            data: 'uri',
                            type: 'text',
                            required: false,
                            readOnly: true
                        };
                        
            var insertionStatusColumn = {
                                        data: 'insertion status',
                                        type: 'text',
                                        required: false,
                                        readOnly: true
                                        };
            var genusListColumn = {
                            data: 'genus',
                            type: 'dropdown',
                            source: genusList,
                            strict: true,
                            required: false
                        };
            var speciesListColumn = {
                            data: 'species',
                            type: 'dropdown',
                            source: speciesList,
                            required: false
                        };
            
            var varietiesListColumn = {
                            data: 'variety',
                            type: 'dropdown',
                            source: varietiesList,
                            required: false
                        };   
            var externalURI = {
                            data: 'externalURI',
                            type: 'text',
                            required: false
                        };

            //table configuration for genus insertion
            if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#Genus") {
                var table = {
                startRows: 3,
                columns: [    
                    uriColumn,
                    {
                        data: 'genus',
                        type: 'text',
                        required: true
                    }, 
                    externalURI,
                    insertionStatusColumn

                ],
                rowHeaders: true,
                colHeaders: [
                    "<b><?= Yii::t('app', 'Generated URI') ?></b>",
                    "<b><?= Yii::t('app', 'Genus label') ?></b>",
                    "<b><?= Yii::t('app', 'Genus URI') ?></b>",

                    "<b><?= Yii::t('app', 'Insertion status') ?></b>"
                ],
                manualRowMove: true,
                manualColumnMove: true,
                contextMenu: true,
                filters: true,
                dropdownMenu: true,
                cells: function(row, col, prop) {
                    var cellProperties = {};

                    if (col === 0 || col === 4) {
                        cellProperties.renderer = readOnlyColumnRenderer;
                    }

                    return cellProperties;
                },
                afterGetColHeader: function (col, th) {
                    if (col === 1 | col === 2 ) {
                        th.style.color = "red";
                    }
                }
             };
             
            //table configuration for species insertion
            } else if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#Species") {
                var table = {
                    startRows: 3,                    
                    columns: [
                        uriColumn,
                        genusListColumn,
                        {                            
                            data: 'speciesLabelEn',
                            type: 'text',
                            required: true
                        },
                        {
                            data: 'speciesLabelFr',
                            type: 'text',
                            required: false
                        },
                        {
                            data: 'speciesLabelLa',
                            type: 'text',
                            required: false
                        },
                        externalURI,
                        insertionStatusColumn      
                    ],
                    rowHeaders: true,
                    colHeaders: [
                        "<b><?= Yii::t('app', 'Generated URI') ?></b>",
                        "<b><?= Yii::t('app', 'Genus        ') ?></b>",
                        "<b><?= Yii::t('app', 'Species Label (En)') ?></b>",
                        "<b><?= Yii::t('app', 'Species Label (Fr)') ?></b>",
                        "<b><?= Yii::t('app', 'Species Label (La)') ?></b>",
                        "<b><?= Yii::t('app', 'Species URI') ?></b>",
                        "<b><?= Yii::t('app', 'Insertion status') ?></b>"
                    ],
                    manualRowMove: true,
                    manualColumnMove: true,
                    contextMenu: true,
                    filters: true,
                    dropdownMenu: true,
                    cells: function(row, col, prop) {
                        var cellProperties = {};

                        if (col === 0 || col === 6) {
                            cellProperties.renderer = readOnlyColumnRenderer;
                        }

                        return cellProperties;
                    },
                    afterGetColHeader: function (col, th) {
                        if ( col === 2 ) {
                            th.style.color = "red";
                        }
                    }
                };
            
            //table configuration for variety insertion
            } else if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#Variety") {
                var table = {
                    startRows: 3,
                    columns: [
                        uriColumn,
                        genusListColumn,
                        speciesListColumn,
                        {
                            data: 'variety',
                            type: 'text',
                            required: true
                        },
                        externalURI,
                        insertionStatusColumn
                    ],
                    rowHeaders: true,
                    colHeaders: [
                        "<b><?= Yii::t('app', 'Generated URI') ?></b>",
                        "<b><?= Yii::t('app', 'Genus') ?></b>",
                        "<b><?= Yii::t('app', 'Species') ?></b>",
                        "<b><?= Yii::t('app', 'Variety') ?></b>",
                        "<b><?= Yii::t('app', 'Variety URI') ?></b>",
                        "<b><?= Yii::t('app', 'Insertion status') ?></b>"
                    ],
                    manualRowMove: true,
                    manualColumnMove: true,
                    contextMenu: true,
                    filters: true,
                    dropdownMenu: true,
                    cells: function(row, col, prop) {
                        var cellProperties = {};

                        if (col === 0 || col === 5) {
                            cellProperties.renderer = readOnlyColumnRenderer;
                        }

                        return cellProperties;
                    },
                    afterGetColHeader: function (col, th) {
                        if (col === 3) {
                            th.style.color = "red";
                        }
                    }
                };
            
            //table configuration for accession insertion
            } else if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#Accession") {
                var table = {
                    startRows: 3,
                    columns: [
                        uriColumn,
                        genusListColumn,
                        speciesListColumn,
                        varietiesListColumn,
                        {
                            data: 'accession',
                            type: 'text',
                            required: true
                        },
                        externalURI,        
                        insertionStatusColumn
                    ],
                    rowHeaders: true,
                    colHeaders: [
                        "<b><?= Yii::t('app', 'Generated URI') ?></b>",
                        "<b><?= Yii::t('app', 'Genus') ?></b>",
                        "<b><?= Yii::t('app', 'Species') ?></b>",
                        "<b><?= Yii::t('app', 'Variety') ?></b>",
                        "<b><?= Yii::t('app', 'Accession') ?></b>",
                        "<b><?= Yii::t('app', 'Accession URI') ?></b>",
                        "<b><?= Yii::t('app', 'Insertion status') ?></b>"
                    ],
                    manualRowMove: true,
                    manualColumnMove: true,
                    contextMenu: true,
                    filters: true,
                    dropdownMenu: true,
                    cells: function(row, col, prop) {
                        var cellProperties = {};

                        if (col === 0 || col === 6) {
                            cellProperties.renderer = readOnlyColumnRenderer;
                        }

                        return cellProperties;
                    },
                    afterGetColHeader: function (col, th) {
                        if (col === 4 ) {
                            th.style.color = "red";
                        }
                    }
                };
                
            //table configuration for lot insertion
            } else if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#PlantMaterialLot") {
                var table = {
                    startRows: 3,
                    columns: [
                        uriColumn,
                        genusListColumn,
                        speciesListColumn,
                        varietiesListColumn,
                        {
                            data: 'accession',
                            type: 'dropdown',
                            source: accessionsList,
                            required: false
                        },
                                        {
                            data: 'lotType',
                            type: 'dropdown',
                            source: lotTypesList,
                            required: true
                        },
                        {
                            data: 'lot',
                            type: 'text',
                            required: true
                        },
                        externalURI,      
                        insertionStatusColumn
                    ],
                    rowHeaders: true,
                    colHeaders: [
                        "<b><?= Yii::t('app', 'Generated URI') ?></b>",
                        "<b><?= Yii::t('app', 'Genus') ?></b>",
                        "<b><?= Yii::t('app', 'Species') ?></b>",
                        "<b><?= Yii::t('app', 'Variety') ?></b>",
                        "<b><?= Yii::t('app', 'Accession') ?></b>",
                        "<b><?= Yii::t('app', 'LotType') ?></b>",
                        "<b><?= Yii::t('app', 'Lot') ?></b>",
                        "<b><?= Yii::t('app', 'Lot URI') ?></b>",
                        "<b><?= Yii::t('app', 'Insertion status') ?></b>"
                    ],
                    manualRowMove: true,
                    manualColumnMove: true,
                    contextMenu: true,
                    filters: true,
                    dropdownMenu: true,
                    cells: function(row, col, prop) {
                        var cellProperties = {};
                        if (col === 0 || col === 8) {
                            cellProperties.renderer = readOnlyColumnRenderer;
                        }
                        return cellProperties;
                    },
                    afterGetColHeader: function (col, th) {
                        if (col === 5 | col === 6 ) {
                            th.style.color = "red";
                        }
                    }
                };
            } 
            
            //Generates the handsontable 
            //it is displayed only if a germplasmType has been selected
            if (germplasmType !== "") { 
                var hotElement = document.querySelector('#germplasms-multiple-insert-table');
                var handsontable = new Handsontable(hotElement, table);  
                $('#germplasms-multiple-insert-button').show();
                $('#import-csv').show();
                $('#documentation-info').show();
            }
      
            //Updates the dropdown lists depending on others lists selection
            handsontable.updateSettings({
                afterChange: function(changes) {
                    changes.forEach(([row, prop, oldVal, newVal]) => {
                        if (prop === 'genus' ) {
                            //if a genus is selected and the germplasm type is not a genus or a species, then we filter the list of species
                            if (germplasmType !== "http://www.opensilex.org/vocabulary/oeso#Genus" && germplasmType !== "http://www.opensilex.org/vocabulary/oeso#Species") {
                                var cell = {};                                
                                $.ajax({
                                    url: 'index.php?r=germplasm%2Fget-species',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {fromGenus: newVal}
                                }).done(function (data) {
                                    cell.source = data;
                                    cell.type = 'dropdown';
                                    handsontable.setCellMetaObject(row,2,cell);
                                });
                                //if a genus is selected and the germplasm type is not a variety, species or genus, then we filter the list of varieties
                                if (germplasmType !== "http://www.opensilex.org/vocabulary/oeso#Variety") {
                                    var cell = {};
                                    cell.type = 'dropdown';
                                    $.ajax({
                                        url: 'index.php?r=germplasm%2Fget-varieties',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {fromGenus: newVal}
                                    }).done(function (data) {
                                        cell.source = data;
                                        handsontable.setCellMetaObject(row,3,cell);
                                    });                                    
                                    //if a genus is selected and the germplasm type is a plantMaterialLot, then we filter the list of accessions
                                    if (germplasmType !== "http://www.opensilex.org/vocabulary/oeso#Accession") {
                                        var cell = {};
                                        cell.type = 'dropdown';
                                        $.ajax({
                                            url: 'index.php?r=germplasm%2Fget-accessions',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {fromGenus: newVal}
                                        }).done(function (data) {
                                            cell.source = data;
                                            handsontable.setCellMetaObject(row,4,cell);
                                        });
                                    }
                                }
                            }
                        }
                        
                        if (prop === 'species' ) {
                            //if a species is selected and the germplasm type is accession or lot, then we filter the list of varieties
                            if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#Accession" || germplasmType === "http://www.opensilex.org/vocabulary/oeso#PlantMaterialLot") {                                
                                var cell = {};
                                cell.type = 'dropdown';
                                $.ajax({
                                    url: 'index.php?r=germplasm%2Fget-varieties',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {fromSpecies: newVal}
                                }).done(function (data) {
                                    cell.source = data;
                                    handsontable.setCellMetaObject(row,3,cell);
                                });
                                //if a species is selected and the germplasm type is a lot, then we filter the list of accessions
                                if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#PlantMaterialLot") {
                                    var cell = {};
                                    cell.type = 'dropdown';
                                    $.ajax({
                                        url: 'index.php?r=germplasm%2Fget-accessions',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {fromSpecies: newVal}
                                    }).done(function (data) {
                                        cell.source = data;
                                        handsontable.setCellMetaObject(row,4,cell);
                                    });                                
                                }
                            }
                        }
                        
                        if (prop === 'variety' ) {     
                            //if a variety is selected and the germplasm type is a lot, then we filter the list of accessions
                            if (germplasmType === "http://www.opensilex.org/vocabulary/oeso#PlantMaterialLot") {
                                var cell = {};
                                cell.type = 'dropdown';
                                $.ajax({
                                    url: 'index.php?r=germplasm%2Fget-accessions',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {fromVariety: newVal}
                                }).done(function (data) {
                                    cell.source = data;
                                    handsontable.setCellMetaObject(row,4,cell);
                                });
                            }
                        }           
                    });
                  handsontable.render();
                }
            });

            /**
             * fill the table with the csv files data             * 
             * @param the imported file
             */
            function fillTheTable(files) {
                //const selectedFile = document.getElementById('input').files[0];
                if (files && files[0]) {
                    Papa.parse(files[0],{
                        complete: function(results) {
                            console.log(results.data);
                            var dataToPut = results.data; 
                            for(var i= 1; i < dataToPut.length; i++) {
                                for (var j=0; j < dataToPut[i].length; j++) {
                                    handsontable.setDataAtCell(i-1,j,dataToPut[i][j]);
                                }
                            }
                        }
                    });

                }    

            }
            /**
             * if the data is valid, calls the insert action
             * @param {boolean} callback
             * @returns
             */
            function add(callback) {
                if (callback) {
                    document.getElementById("loader").style.display = "block";
                    document.getElementById("germplasms-creation").style.display = "none";

                    germplasmsArray = handsontable.getData();
                    germplasmsString = JSON.stringify(germplasmsArray);
                    lastColumnNumber = handsontable.countCols()-1;
                    
                    $.ajax({
                        url: 'index.php?r=germplasm%2Fcreate-multiple-germplasm',
                        type: 'POST',
                        dataType: 'json',
                        data: {germplasms: germplasmsString,
                                germplasmType: germplasmType}
                    }).done(function (data) {
                        document.getElementById("germplasms-creation").style.display = "block";
                        document.getElementById("loader").style.display = "none";
                        for (var i = 0; i < data["messages"].length; i++) {   
                            if (data["germplasmUris"][i] !== null) {
                                handsontable.setDataAtCell(i, 0, data["germplasmUris"][i]);
                            }
                            handsontable.setDataAtCell(i, lastColumnNumber, data["messages"][i]);
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
            
            //save germplasms
            $(document).on('click', '#germplasms-save', function() {
                handsontable.validateCells(add);
            });

        </script>
    </div>
</div>   