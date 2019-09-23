<?php

//******************************************************************************
//                                       YiiDataModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;

/**
 * Model for the data. Used with web services
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiScientificAppModel extends WSActiveRecord {
    
    /**
     * 
     * @param string $pageSize number of elements per page
     * @param string $page current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new \app\models\wsModels\WSDataAnalysisModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $page != "") ? $page : null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() { 
        return [
        ];
    }
    
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented');
    }    
}