<?php

namespace app\models\yiiModels;

use yii\base\DynamicModel;

class DataAnalysisApp extends DynamicModel {

    private $formParameters;

    const BOOLEAN_TYPE = "boolean";

    /**
     * 
     * @param array $attributes
     * @param type $config
     * @param type $scriptParameters
     */
    public function __construct(array $attributes = array(), $config = array(), $scriptParameters = []) {
        parent::__construct($attributes, $config);
        $this->formParameters = $scriptParameters;
        $this->setModelRules();
    }

    /**
     * 
     * @return type
     */
    function getScriptParameters() {
        return $this->formParameters;
    }

    /**
     * 
     * @param type $names
     * @param type $except
     * @return type
     */
    public function getAttributesForHTTPClient($names = null, $except = array()) {
        $attributes = parent::getAttributes($names, $except);
        foreach ($attributes as $attribute => $attributeValue) {
            if (isset($this->formParameters[$attribute]) &&
                    array_key_exists("type", $this->formParameters[$attribute]) &&
                    $this->formParameters[$attribute]['type'] == self::BOOLEAN_TYPE) {

                $attributes[$attribute] = filter_var($attributeValue, FILTER_VALIDATE_BOOLEAN);
            }
        }
        $attributes = array_filter(
                $attributes, function($val) {
            return $val !== null;
        });
        return $attributes;
    }

    public function load($data, $formName = null) {
        $valid = parent::load($data, $formName);
        if ($this->formName() != null && isset($data[$this->formName()])) {
            foreach ($data[$this->formName()] as $attribute => $attributeValue) {
                if (isset($this->formParameters[$attribute]) &&
                        array_key_exists("type", $this->formParameters[$attribute]) &&
                        $this->formParameters[$attribute]['type'] == self::BOOLEAN_TYPE) {

                    $this->$attribute = $attributeValue;
                } else {
                    if (!empty($attributeValue)) {
                        $this->$attribute = $attributeValue;
                    }
                }
            }
        }
        return $valid;
    }

    /**
     * 
     * @param array $parameters
     * @param DynamicModel $model
     */
    private function setModelRules() {
        foreach ($this->formParameters as $parameter => $parameterOptions) {
            if (array_key_exists("required", $parameterOptions)) {
                $this->addRule($parameter, "required");
            }
        }
    }

}
