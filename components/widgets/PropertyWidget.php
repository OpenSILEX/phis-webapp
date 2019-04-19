<?php
//******************************************************************************
//                             PropertyWidget.php
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
 * @author Vincent Migot < vincent.migot@inra.fr>
 */
abstract class PropertyWidget extends Widget {

    const NO_PROPERTY = "No Specific Property";

    // widget title
    public $title;
    
    // URI of the main object (widget option)
    public $uri;
    
    // is URI required or not
    public $isUriRequired = false;
    
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
    private $alias;
    
    // Internal representation of fields array, corresponding to $relationOrder option
    private $fields = [];
    
    // Internal representation of extrafields array, corresponding to properties not described in $relationOrder option
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
        
        if ($this->uri === null && $this->isUriRequired) {
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
     * Construct a "fields" array indexed according to $relationIndex
     * @param array $fields the initial array which will be modified and returned
     * @param object $property
     * @param string $relationIndex
     * @return Array modified fields array
     * @example
     * [
     *   [0] => [
     *       [label] => "type"
     *       [values] => [
     *           [0] => [
     *               [relation] => http://www.w3.org/1999/02/22-rdf-syntax-ns#type
     *               [uri] => http://www.opensilex.org/vocabulary/oeso#Installation
     *               [label] => installation
     *               [type] => http://www.w3.org/2002/07/owl#Class
     *               [typeLabel] => 
     *           ]
     *       ]
     *    ],
     *   [1] => [
     *       [label] => "has part"
     *       [values] => [
     *               [0] => [
     *                       [relation] => http://www.opensilex.org/vocabulary/oeso#hasPart
     *                       [uri] => http://www.phenome-fppn.fr/m3p/eo/es2/roof
     *                       [label] => PhenoArch: roof
     *                       [type] => http://www.opensilex.org/vocabulary/oeso#Greenhouse
     *                       [typeLabel] => greenhouse
     *               ],
     *               [1] => [
     *                       [relation] => http://www.opensilex.org/vocabulary/oeso#hasPart
     *                       [uri] => http://www.phenome-fppn.fr/m3p/es2
     *                       [label] => PhenoArch: greenhouse
     *                       [type] => http://www.opensilex.org/vocabulary/oeso#Greenhouse
     *                       [typeLabel] => greenhouse
     *               ]
     *       ]
     *    ]
     * ]
     */
    private function constructFields($fields, $property, $relationIndex) {
        // If the given relationIndex doesn't exists in fields array, initialize it.
        if (!isset($fields[$relationIndex])) {
            $label = null;

            if (count($property->relationLabels) > 0) {
                // If property has at least one relation label, use the first one
                // It may be the prefered label or the first of standart (rdf) label
                $label = $property->relationLabels[0];
            } else {
                // Otherwise use the last part of the relation uri as label (after # sign)
                $parts = explode('#', $property->relation);
                if (count($parts) == 1) {
                    $parts = explode('/', $property->relation);
                }
                $label = end($parts);
            }

            // Initialize the fields index with the label and an empty array as values
            $fields[$relationIndex] = [
                "label" => $label,
                "values" => []
            ];
        }

        // Initialize value label with raw value as default
        $valueLabel = $property->value;
        if (count($property->valueLabels) > 0) {
            // If property has at least one value label, use the first one
            // It may be the prefered label or the first of standart (rdf) label            
            $valueLabel = $property->valueLabels[0];
        } else {
            // Otherwise use the last part of the value uri as label (after # sign)
            // If there is no #, it will use the full value
            $parts = explode('#', $property->value);
            if (count($parts) == 1) {
                $parts = explode('/', $property->value);
            }
            $valueLabel = end($parts);
        }

        $typeLabel = null;
        if (count($property->rdfTypeLabels) > 0) {
            // If property has at least one rdf type label, use the first one
            // It may be the prefered label or the first of standart (rdf) label  
            $typeLabel = $property->rdfTypeLabels[0];
        }

        // Add the constructed value on the relation index
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
        if(count($this->properties) == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', self::NO_PROPERTY) . "</h3>";
        }
        else {
            $htmlRendered = "<h3>" . $this->title . "</h3>";
            $rows = [];

            if ($this->uri !== null) {
                $rows[] = $this->renderAttribute("uri", $this->uri);
            }
            if ($this->alias !== null) {
                $rows[] = $this->renderAttribute("alias", $this->alias);
            }

            foreach ($this->fields as $field) {
                $rows[] = $this->renderAttribute($field["label"], $field["values"]);
            }

            foreach ($this->extraFields as $field) {
                $rows[] = $this->renderAttribute($field["label"], $field["values"]);
            }

            $htmlRendered .= implode("\n", $rows);
        }
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');

        echo Html::tag($tag, $htmlRendered, $options);
    }

    /**
     * Returns the rendered string corresponding to a row.
     * @param string $key
     * @param array $values
     * @return string
     */
    protected function renderAttribute($key, $values) {
        if (is_array($values)) {
            if (count($values) > 1) {
                $valuesString = "<ul>";

                $valuesString .= $this->renderValues($values);

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
     * Returns the rendered string corresponding to a list of values.
     * @param array $values
     * @return string
     */
    protected abstract function renderValues($values);

    /**
     * Return the rendering string corresponding to a value, looking for its formatter
     * @param array $value
     * @return string
     */
    protected abstract function renderValue($value);
}
