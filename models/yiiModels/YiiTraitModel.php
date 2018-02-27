<?php

//**********************************************************************************************
//                                       YiiTraitModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 27 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 27 2017
// Subject: The Yii model for the traits. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSTraitModel;

class YiiTraitModel extends YiiInstanceDefinitionModel {
     /**
     * Initialise le wsModel. Comme on est dans le modèle des Traits, 
     * wsModel est de type WSTraitModel
     * @param String $pageSize le nombre d'éléments par page 
     *                               (pour les retours du ws - limité à 150 000)
     * @param String $page le numéro de la page courante qui est consultée
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSTraitModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
}
