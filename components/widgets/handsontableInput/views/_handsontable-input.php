<?php
//******************************************************************************
//                       _handsontable-input.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//****************************************************************************** 
namespace app\components\widgets\handsontableInput\views;

use Yii;
?>

<div class="form-group">
    <label class="control-label">Concerned items</label>
    <div id='handsontable-<?= $id ?>'></div>
    <div class="<?= $actionButtonsGroupDivClass ?>">
        <button type="button" id="<?= $addRowButtonId ?>" class="btn btn-primary" title="<?= Yii::t("app", "Add row")?>">
            <span class="glyphicon glyphicon-plus"></span>
        </button>
        <button id="<?= $removeRowButtonId ?>" class="btn btn-danger" title="<?= Yii::t("app", "Remove last row")?>">
            <span class="glyphicon glyphicon-minus"></span>
        </button>
    </div>
    <div id="<?= $inputGroupDivId ?>" style="display:none"></div>
</div>