<?php

//******************************************************************************
//                                       _form_dataset_created.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 févr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 févr. 2018
// Subject: render the data inserted via the form 
//******************************************************************************

/* @var $this yii\web\View */
/* @var $model app\models\YiiDatasetModel */
/* @var $handsontable openSILEX\handsontablePHP\adapter\HandsontableSimple */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Dataset created');
?>

<?= $handsontable->loadJSLibraries(true) ?>
<?= $handsontable->loadCSSLibraries() ?>

<div class="dataset-created-render well">
    
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert alert-success">
        <h4><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> <?= $insertedDataNumber ?> data inserted : </h4>
        <!--<p>Inserted data summary : </p>-->
    </div>
    
    <div id="<?= $handsontable->getContainerName() ?>">
    </div>
    <script>
        <?= $handsontable->generateJavascriptCode() ?>
    </script>
</div>

