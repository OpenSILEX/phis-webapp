<?php

//**********************************************************************************************
//                                       YiiVariableModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: November, 24 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  November, 24 2017
// Subject: The Yii model for the variables. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSVariableModel;
use Yii;

require_once(__DIR__ . '/../../config/config.php');

class YiiVariableModel extends YiiInstanceDefinitionModel {
    
    /**
     *
     * @var YiiTraitModel trait
     * @var YiiMethodModel method
     * @var YiiUnitModel unit
     */
    public $trait;
    const VARIABLE_TRAIT = "trait"; //called VARIABLE_TRAIT because trait is a php concept
    public $method;
    const METHOD = "method";
    public $unit;
    const UNIT = "unit";
    
    /**
     * Initialise le wsModel. Comme on est dans le modèle des Variables, 
     * wsModel est de type WSVariableModel
     * @param String $pageSize le nombre d'éléments par page 
     *                               (pour les retours du ws - limité à 150 000)
     * @param String $page le numéro de la page courante qui est consultée
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSVariableModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    public function rules() {
        $toReturn = parent::rules();
        $toReturn[] = [['uri', 'label'], 'required'];
        $toReturn[] = [['trait', 'method', 'unit'], 'safe'];
        
        return $toReturn;
    }
    
    public function attributeLabels() {
        $toReturn = parent::attributeLabels();
        $toReturn['trait'] = \Yii::t('app', 'Trait');
        $toReturn['method'] = \Yii::t('app', 'Method');
        $toReturn['unit'] = \Yii::t('app', 'Unit');
        $toReturn['label'] = \Yii::t('app', 'Variable Label');
        $toReturn['comment'] = \Yii::t('app', 'Variable Definition');
        
        return $toReturn;
    }
    
    /**
     * Permet de remplir les attributs en fonction des informations comprises 
     * dans le tableau passé en paramètre
     * @param array $array tableau clé=>valeur contenant les valeurs des attributs de variable
     */
    protected function arrayToAttributes($array) {
        parent::arrayToAttributes($array);
        
        $trait = new YiiInstanceDefinitionModel();
        $trait->arrayToAttributes($array[YiiVariableModel::VARIABLE_TRAIT]);
        $this->trait = $trait;
        
        $method = new YiiInstanceDefinitionModel();
        $method->arrayToAttributes($array[YiiVariableModel::METHOD]);
        $this->method = $method;
        
        $unit = new YiiInstanceDefinitionModel();
        $unit->arrayToAttributes($array[YiiVariableModel::UNIT]);
        $this->unit = $unit;
    }

    public function attributesToArray() {
        $toReturn = parent::attributesToArray();
        if (isset($this->trait->uri)) {
            $toReturn[YiiVariableModel::VARIABLE_TRAIT] = $this->trait->uri;
        } else if (is_string ($this->trait)){
            $toReturn[YiiVariableModel::VARIABLE_TRAIT] = $this->trait;
        } else {
            $toReturn[YiiVariableModel::VARIABLE_TRAIT] = null;
        }
        
        if (isset($this->method->uri)) {
            $toReturn[YiiVariableModel::METHOD] = $this->method->uri;
        } else if (is_string ($this->method)){
            $toReturn[YiiVariableModel::METHOD] = $this->method;
        } else {
            $toReturn[YiiVariableModel::METHOD] = null;
        }
        
        if (isset($this->unit->uri)) {
            $toReturn[YiiVariableModel::UNIT] = $this->unit->uri;
        } else if (is_string ($this->unit)){
            $toReturn[YiiVariableModel::UNIT] = $this->unit;
        } else {
            $toReturn[YiiVariableModel::UNIT] = null;
        }
        
        return $toReturn;
    }
    
    /**
     * permet de remplir les champs de description de la variable en fonction de l'uri
     * @param String $sessionToken le token de session
     * @param String $uri l'uri de la variable dont on souhaite avoir les informations
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        $requestRes = $this->wsModel->getVariableByURI($sessionToken, $uri, $params);

        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->arrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * 
     * @return array la liste des Concepts permettant de définir une variable
     * (trait, methode, unité, variable)
     */
    public function getEntitiesConceptsLabels() {
        //SILEX:todo
        //Il faudra mettre en place un service permettant d'avoir ces types 
        //et leurs labels pour éviter d'avoir tout ça en dur dans le code.
        //\SILEX:todo
        return [
            \config::path()['cVariable'] => Yii::t('app', 'Variable'),
            \config::path()['cTrait'] => Yii::t('app', 'Trait'),
            \config::path()['cMethod'] => Yii::t('app', 'Method'),
            \config::path()['cUnit'] => Yii::t('app', 'Unit')
        ];
    }
}
