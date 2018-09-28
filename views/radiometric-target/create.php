<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiSensorModel */

$this->title = Yii::t('yii', 'Add Radiometric Targets');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="radiometric-target-create">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div id="radiometric-table-root">
        <div class="radiometric-form-model">
            <!-- 
                Alias
                URI
                Constructeur
                Numéro de série
                Date de mise en service
                Date d'achat
                Date de dernière calibration
                Installation
                Responsable
            
                Matière: Liste déroulante 
                Forme: Liste déroulante
                Selon la forme switch entre Lxl et D
                BRDF P1
                BRDF P2
                BRDF P3
                BRDF P4
            -->
        </div>
    </div>
    <script>
        $.ready(function() {
            // Init main Handson table
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
                    validator: sensorTypeValidator
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
                if (col === 1 | col === 2 | col === 3 | col === 6 | col === 8) {
                    th.style.color = "red";
                }
            }
         });
            // Add events change
            
            // onSubmit
        });
    </script>
</div>