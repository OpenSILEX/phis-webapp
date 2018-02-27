<?php

//**********************************************************************************************
//                                       YiiMethodModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 27 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 27 2017
// Subject: The Yii model for the methods. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSMethodModel;

/**
 * The yii model for the experiments. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSMethodModel
 * @see app\models\wsModels\YiiInstanceDefinitionModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiMethodModel extends YiiInstanceDefinitionModel {
    
     /**
     * Initialize wsModel. In this class, wsModel is a WSAgronomicalObjectModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSMethodModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
}
