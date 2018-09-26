<?php

//******************************************************************************
//                                       PropertiesWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 20 September, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\helpers\PropertyFormatter;
use Yii;

/**
 * A widget used to generate a customizable object properties grid
 * 
 * @author Vincent Migot < vincent.migot@inra.fr>
 */
class PropertiesWidget extends Widget {

    // Uri of the main object (widget option)
    public $uri;
    // Property type which match the alias value (widget option)
    public $aliasProperty;
    // List of object properties (widget option)
    public $properties;
    // Array of rdf type, if corresponding values found in $properties, 
    // it will display it first in the given order (widget option)
    public $relationOrder;
    // Basic template rendering option (widget option)
    public $template = '<tr><th>{label}</th><td>{value}</td></tr>';
    // Basic table options
    public $options = ['class' => 'table table-striped table-bordered properties-widget'];
    // Alias of the main object determined from aliasProperty options
    private $alias = "";
    // Internal representation of fields array, corresponding to $relationOrder option
    private $fields = [];
    // Internal representation of extrafields array, corresponding to properties not described in $relationOrder otpion
    private $extraFields = [];
    // Mapping of formatters based on rdf type value
    protected $propertyFormatters = [
        "Infrastructure" => PropertyFormatter::INFRASTRUCTURE,
        "LocalInfrastructure" => PropertyFormatter::INFRASTRUCTURE,
        "NationalInfrastructure" => PropertyFormatter::INFRASTRUCTURE,
        "EuropeanInfrastructure" => PropertyFormatter::INFRASTRUCTURE,
        "Installation" => PropertyFormatter::INFRASTRUCTURE,
        "source" => PropertyFormatter::EXTERNAL_LINK
    ];

    /**
     * Construct all widget members based on properties
     * @throws \Exception
     */
    public function init() {
        parent::init();

        $formatters = [];
        foreach ($this->propertyFormatters as $key => $property) {
            if (array_key_exists($key, Yii::$app->params)) {
                $formatters[Yii::$app->params[$key]] = $property;
            } else {
                $formatters[$key] = $property;
            }
        }
        $this->propertyFormatters = $formatters;

        // must be not null
        if ($this->uri === null) {
            throw new \Exception("URI isn't set");
        }

        if ($this->properties === null) {
            throw new \Exception("Properties aren't set");
        }

        // must be an array
        if (!is_array($this->properties)) {
            throw new \Exception("Property list is not an array");
        }

        $props = $this->properties;

        // Get alias from properties if specified and remove corresponding property
        if ($this->aliasProperty !== null) {
            foreach ($props as $i => $property) {

                if ($property->relation === $this->aliasProperty) {
                    $this->alias = $property->value;

                    array_splice($props, $i, 1);
                    break;
                }
            }
        }

        // Construct fields attribute depending on relation order if specified
        if (is_array($this->relationOrder)) {
            foreach ($props as $i => $property) {
                $relationIndex = array_search($property->relation, $this->relationOrder);
                if ($relationIndex !== false) {
                    $this->fields = $this->constructFields($this->fields, $property, $relationIndex);
                    unset($props[$i]);
                }
            }
        }

        // Construct extra fields attribute based on unknown remaing properties
        foreach ($props as $i => $property) {
            $this->extraFields = $this->constructFields($this->extraFields, $property, $property->relation);
        }
    }

    /**
     * Construct a "fields" array indexed by relation uri
     * @param array $fields
     * @param object $property
     * @param string $relationIndex
     * @return Array modified fields array
     *      
     * ]
     */
    private function constructFields($fields, $property, $relationIndex) {
        if (!isset($fields[$relationIndex])) {
            $label = null;
            if (count($property->relationLabels) > 0) {
                $label = $property->relationLabels[0];
            } else {
                $parts = explode('#', $property->relation);
                if (count($parts) == 1) {
                    $parts = explode('/', $property->relation);
                }
                $label = end($parts);
            }

            $fields[$relationIndex] = [
                "label" => $label,
                "values" => []
            ];
        }

        $valueLabel = $property->value;
        if (count($property->valueLabels) > 0) {
            $valueLabel = $property->valueLabels[0];
        } else {
            $parts = explode('#', $property->value);
            if (count($parts) == 1) {
                $parts = explode('/', $property->value);
            }
            $valueLabel = end($parts);
        }

        $typeLabel = null;
        if (count($property->rdfTypeLabels) > 0) {
            $typeLabel = $property->rdfTypeLabels[0];
        }

        $fields[$relationIndex]["values"][] = [
            "relation" => $property->relation,
            "uri" => $property->value,
            "label" => $valueLabel,
            "type" => $property->rdfType,
            "typeLabel" => $typeLabel
        ];

        return $fields;
    }

    /**
     * Render widget
     */
    public function run() {
        $rows = [];

        $rows[] = $this->renderAttribute("uri", $this->uri);
        $rows[] = $this->renderAttribute("alias", $this->alias);

        foreach ($this->fields as $field) {
            $rows[] = $this->renderAttribute($field["label"], $field["values"]);
        }

        foreach ($this->extraFields as $field) {
            $rows[] = $this->renderAttribute($field["label"], $field["values"]);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');
        echo Html::tag($tag, implode("\n", $rows), $options);
    }

    /**
     * Return the rendering string corresponding to a row
     * @param string $key
     * @param array $values
     * @return string
     */
    protected function renderAttribute($key, $values) {
        if (is_array($values)) {
            if (count($values) > 1) {
                $valuesString = "<ul>";

                $valuesString .= $this->renderAttributes($values);

                $valuesString .= "</ul>";
            } else {
                $valuesString = $this->renderValue($values[0]);
            }
        } else {
            $valuesString = $values;
        }

        return strtr($this->template, [
            '{label}' => $key,
            '{value}' => $valuesString,
        ]);
    }

    /**
     * Return the rendering string corresponding to a list of values
     * @param array $values
     * @return string
     */
    protected function renderAttributes($values) {
        $valuesString = "";
        foreach ($values as $value) {
            $valuesString .= "<li>" . $this->renderValue($value) . "</li>";
        }

        return $valuesString;
    }

    /**
     * Return the rendering string corresponding to a value, looking for its formatter
     * @param array $value
     * @return string
     */
    protected function renderValue($value) {
        if ($value['relation'] && array_key_exists($value['relation'], $this->propertyFormatters)) {
            return call_user_func(array("app\components\helpers\PropertyFormatter", $this->propertyFormatters[$value['relation']]), $value);
        } elseif ($value['type'] && array_key_exists($value['type'], $this->propertyFormatters)) {
            return call_user_func(array("app\components\helpers\PropertyFormatter", $this->propertyFormatters[$value['type']]), $value);
        } else {
            return PropertyFormatter::defaultFormat($value);
        }
    }

}
