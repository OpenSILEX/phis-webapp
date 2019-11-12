<?php

//**********************************************************************************************
//                                       YiiDatasetModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 2 2017
// Subject: The Yii model for the dataset. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\yiiModels\YiiDatasetModel;
use app\models\wsModels\WSDatasetModel;

/**
 * Model for the dataset. Used with web services
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiDataSensorModel extends YiiDatasetModel {

  /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        $rules =  parent::rules();
        $rules[] =[['provenanceSensingDevices'], 'required'];
        return $rules;
    }

}
