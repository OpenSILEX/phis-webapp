<?php

//******************************************************************************
//                                       YiiInfrastructureModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 Aug, 2018
// Contact: morgane.vidal@inra.fr, vincent.migot@inra.fr,  anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSInfrastructureModel;
use app\models\wsModels\WSConstants;

/**
 * The Yii model for the infrastructures. Implements a customized Active Record
 * (WSActiveRecord, for the web service access).
 * @update [Andréas Garcia] 15 Feb., 2019: use Property model + coding style
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiInfrastructureModel extends WSActiveRecord {
    
    /**
     * The URI of the infrastructure. 
     * @example http://www.phenome-fppn.fr/diaphen
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
    public $label;
    const ALIAS = "label";
    const ALIAS_LABEL = "Alias";
    
    /**
     * The type of the infrastructure. It is an uri corresponding to an ontology concept.
     *  (e.g. oepo:Infrastructure)
     * @var string
     */
    public $rdfType;
    const RDF_TYPE = "rdfType";
    const RDF_TYPE_LABEL = "Type";
    
    /**
     * The documents associated to the infrastructure.
     * @see \app\models\yiiModels\YiiDocumentModel
     * @var array<YiiDocumentModel>
     */
    public $documents;
    const DOCUMENTS = "documents";
    
    /**
     * The properties of the infrastructure
     * @var array 
     */
    public $properties;
    const PROPERTIES = "properties";
    const RELATION = "relation";
    const VALUE = "value";
    const RDF_TYPE_LABELS = "rdfTypeLabels";
    const RELATION_LABELS = "relationLabels";
    const VALUE_LABELS = "valueLabels";
    
    /**
     * Initialize wsModel. In the first version of this class, there is no WSModel.
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSInfrastructureModel();
        
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
    
    public function getDetails($sessionToken, $uri, $lang) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[WSConstants::PAGE] = $this->page;
        }
        
        $params[WSConstants::LANG] = $lang;
                
        $requestRes = $this->wsModel->getInfrastructureDetails($sessionToken, $uri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->uri = $uri;
                if ($requestRes[self::PROPERTIES] !== null) {
                    foreach ($requestRes[self::PROPERTIES] as $propertyInArray) {
                        $property  = new YiiPropertyModel();
                        $property->arrayToAttributes($propertyInArray);
                        
                        if ($property->relation == Yii::$app->params["rdfsLabel"]) {
                            $this->label = $property->value;
                        }
                        $this->properties[] = $property;
                    } 
                } 
                return $this;
            }
        } else {
            return $requestRes;
        }
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
     * Map Array to Object
     * @see \app\models\wsModels\WSActiveRecord::arrayToAttributes($array)
     * @param array $array
     * @throws Exception
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[self::URI];
        $this->label = $array[self::ALIAS];
        $this->rdfType = $array[self::RDF_TYPE];
    }

    /**
     *  Map Object to Array
     * @see \app\models\wsModels\WSActiveRecord::attributesToArray($array)
     * @param array $array
     * @throws Exception
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[self::URI] = $this->uri;
        $elementForWebService[self::ALIAS] = $this->label;
        $elementForWebService[self::RDF_TYPE] = $this->rdfType;
        $elementForWebService[\app\models\wsModels\WSConstants::LANG] = Yii::$app->language;
        
        return $elementForWebService;
    }
}
