<?php

//******************************************************************************
//                                       YiiInfrastructureModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 Aug, 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;

/**
 * SILEX:warning
 * In this first version, there are no access to the web service. Data is static.
 * \SILEX:warning
 * The Yii model for the infrastructures. Implements a customized Active Record
 * (WSActiveRecord, for the web service access).
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiInfrastructureModel extends \app\models\wsModels\WSActiveRecord {
    /**
     * The URI of the infrastructure. 
     *  (e.g. http://www.phenome-fppn.fr/diaphen)
     * @var string
     */
    public $uri;
    const URI = "uri";
    const URI_LABEL = "URI";
    /**
     * The alias of the infrastrucure.
     *  (e.g. Diaphen)
     * @var string
     */
    public $alias;
    const ALIAS = "alias";
    const ALIAS_LABEL = "Alias";
    /**
     * The type of the infrastructure. It is an uri corresponding to an ontology concept.
     *  (e.g. oepo:Infrastructure)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "type";
    const RDF_TYPE_LABEL = "Type";
    /**
     * The documents associated to the infrastructure.
     * @see \app\models\yiiModels\YiiDocumentModel
     * @var array<YiiDocumentModel>
     */
    public $documents;
    const DOCUMENTS = "documents";
    
    /**
     * Initialize wsModel. In the first version of this class, there is no WSModel.
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        //SILEX:info
        //Uncomment the following line when the infrastructure service will be 
        //deployed and the WSInfrastructureModel created.
        //$this->wsModel = new WSInfrastructureModel();
        //\SILEX:info
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * @see yii\base\Model::rules()
     * @see https://www.yiiframework.com/doc/guide/2.0/en/input-validation
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [[YiiInfrastructureModel::URI, YiiInfrastructureModel::ALIAS, YiiInfrastructureModel::RDF_TYPE], 'required'],  
          [[YiiInfrastructureModel::DOCUMENTS], 'safe'],
        ];
    }
    
    /**
     * @see yii\base\Model::attributeLabels()
     * @see https://www.yiiframework.com/doc/api/2.0/yii-base-model#attributeLabels()-detail
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            YiiInfrastructureModel::URI => YiiInfrastructureModel::URI_LABEL,
            YiiInfrastructureModel::ALIAS => Yii::t('app', YiiInfrastructureModel::ALIAS_LABEL),
            YiiInfrastructureModel::RDF_TYPE => Yii::t('app', YiiInfrastructureModel::RDF_TYPE_LABEL),
        ];
    }

    /**
     * Not implemented yet. Override WSActiveRecord::attributesToArray($array)
     * @see \app\models\wsModels\WSActiveRecord::arrayToAttributes($array)
     * @param array $array
     * @throws Exception
     */
    protected function arrayToAttributes($array) {
        throw new Exception('Not implemented yet.');
    }

    /**
     * Not implemented yet. Override WSActiveRecord::attributesToArray($array)
     * @see \app\models\wsModels\WSActiveRecord::attributesToArray($array)
     * @param array $array
     * @throws Exception
     */
    public function attributesToArray() {
        throw new Exception('Not implemented yet.');
    }
}
