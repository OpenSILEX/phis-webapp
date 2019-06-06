<?php
//******************************************************************************
//                       HandsontableInputWidgetAsset.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\handsontableInput;

use yii\web\AssetBundle;

/**
 * Asset for the handsontable input widget.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class HandsontableInputWidgetAsset extends AssetBundle {
    
    public $js = [
        'js/handsontable-input-widget.js'
    ];

    public $css = [
        'css/handsontable-input-widget.css'
    ];

    public $depends = [
        'himiklab\handsontable\HandsontableAsset'
    ];
    
    public function init()
    {
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}
