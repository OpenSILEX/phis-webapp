<?php
//******************************************************************************
//                         PropertyWidgetWithoutActions.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 20 Sept. 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use app\components\widgets\PropertyWidget;
use app\components\helpers\PropertyFormatter;

/**
 * Widget to simply render properties.
 * @author Vincent Migot < vincent.migot@inra.fr>
 */
class PropertyWidgetWithoutActions extends PropertyWidget {

    /**
     * Returns the rendered string corresponding to a list of values
     * @param array $values
     * @return string
     */
    protected function renderValues($values) {
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
