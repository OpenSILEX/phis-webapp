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

/**
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
        $this->wsModel = new \app\models\wsModels\WSInfrastructureModel();
        
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
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $params[\app\models\wsModels\WSConstants::LANG] = $lang;
                
        $requestRes = $this->wsModel->getInfrastructureDetails($sessionToken, $uri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->uri = $uri;
                $this->propertiesArrayToAttributes($requestRes);
                return $this;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * allows to fill the property attribute with the information of the given array
     * @param array $array array key => value with the properties of an object 
     * (corresponding to a sensor profile)
     */
    protected function propertiesArrayToAttributes($array) {
        if ($array[self::PROPERTIES] !== null) {
            foreach ($array[self::PROPERTIES] as $property) {
                $propertyToAdd = null;
                $propertyToAdd[self::RELATION] = $property->relation; 
                $propertyToAdd[self::VALUE] = $property->value;
                $propertyToAdd[self::RDF_TYPE] = $property->rdfType;
                $propertyToAdd[self::RELATION_LABELS] = $property->relationLabels; 
                $propertyToAdd[self::VALUE_LABELS] = $property->valueLabels;
                $propertyToAdd[self::RDF_TYPE_LABELS] = $property->rdfTypeLabels;     
                
                if ($property->relation == Yii::$app->params["rdfsLabel"]) {
                    $this->label = $property->value;
                }
                $this->properties[] = $property;
            }
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
        $elementForWebService[self::URI] = $this->uri;
        $elementForWebService[self::ALIAS] = $this->label;
        $elementForWebService[self::RDF_TYPE] = $this->rdfType;
        
        return $elementForWebService;
    }
}
