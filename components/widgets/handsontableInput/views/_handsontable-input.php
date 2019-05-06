<?php
//******************************************************************************
//                       _handsontable-input.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//****************************************************************************** 
namespace app\components\widgets\handsontableInput\views;

use yii\helpers\Html;

?>

<div class="<?= $actionButtonsGroupDivClass ?>">
<?= 
    Html::buttonInput("Add row", [
        'id' => $addRowButtonId,
        'class' => "btn btn-primary"
    ])
?>  
<?=
    Html::buttonInput("Remove last row", [
        'id' => $removeRowButtonId,
        'class' => "btn btn-danger"
    ])
?>
</div>
<div id='handsontable-<?= $id ?>'></div>
<div id="<?= $inputGroupDivId ?>" style=\"display:none\"></div>