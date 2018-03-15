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
use yii\helpers\Url;
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js"></script>

<div class="dataset-form">
    <div id="dataset-multiple-insert-table"></div>

    <script>
    
        //get variables list
        var variables;
        $.ajaxSetup({async: false});
            $.ajax({
                url: '<?php echo Url::to(['variable/get-variables-uri-and-alias']) ?>',
                type: 'GET',
                processData: false,
                datatype: 'json'
            }).done(function (data) {
                variables = JSON.parse(data);
            }).fail(function (jqXHR, textStatus) {
                                    //SILEX:todo
                                    //gestion messages d'erreur
                                    //\SILEX:todo
                                    alert("ERROR : " + jqXHR);
           });
           
           //generate handsontable
            var hotElement = document.querySelector('#dataset-multiple-insert-table');        
            var handsontable = new Handsontable(hotElement, {
               startRows: 2,
               columns: [
                   {
                       data: 'alias',
                       type: 'text',
                       required: true
                   },
                   {
                       data: 'type',
                       type: 'text',
                       required: true
                   },
                   {
                       data: 'brand',
                       type: 'text',
                       required: true
                   },
                   {
                       data: 'variable',
                       type: 'dropdown',
                       source: variables,
                       required: true
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
                   '<?= Yii::t('app', 'Alias') ?>',
                   '<?= Yii::t('app', 'Type') ?>',
                   '<?= Yii::t('app', 'Brand') ?>',
                   '<?= Yii::t('app', 'Variable') ?>',
                   '<?= Yii::t('app', 'In Service Date') ?>',
                   '<?= Yii::t('app', 'Date Of Purchase') ?>',
                   '<?= Yii::t('app', 'Date Of Last Calibration') ?>',
                   '<?= Yii::t('app', 'Document URI') ?>'
               ],
               manualRowMove: true,
               manualColumnMove: true,
               contextMenu: true,
               filters: true,
               dropdownMenu: true
            });
    </script>
</div>