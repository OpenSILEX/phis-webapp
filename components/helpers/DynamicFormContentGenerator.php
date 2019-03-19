<?php

//******************************************************************************
//                                       DynamicFormContentGenerator.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 19 Mars, 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\helpers;

use kartik\date\DatePicker;
use kartik\select2\Select2;
/**
 * Helper which regroups function to create a form dynamically from a configuration
 * array 
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DynamicFormContentGenerator {

    public static function generateFormContent($form, $model, $inputParameters, $valueParameters) {
       
        // construct form
        foreach ($model as $key => $attribute) {
            if (isset($inputParameters[$key]['visibility']) && $inputParameters[$key]['visibility'] === "hidden") {
                echo $form->field($model, $key)->hiddenInput()->label(false);
            } else {
                if (isset($inputParameters[$key]['type'])) {

                    switch ($inputParameters[$key]['type']) {
                        case 'string':
                            echo $form->field($model, $key)->label($inputParameters[$key]['label']);
                            break;
                        case 'date':
                            echo $form->field($model, $key)->widget(DatePicker::className(), [
                                'options' => [
                                    'placeHolder' => date($inputParameters[$key]['dateValue'])],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => $inputParameters[$key]['format']
                                ]
                            ])->label($inputParameters[$key]['label']);
                            break;
                        case 'boolean':
                            echo $form->field($model, $key)->dropDownList([
                                false => 'Sans',
                                true => 'Avec'
                            ]);
                            break;
                        case 'list':
                            $multiple = true;
                            $pluginOptions = [
                                'allowClear' => false,
                            ];
                            $option = [
                                'placeholder' => $inputParameters[$key]['label']
                            ];
                            if (isset($inputParameters[$key]['maxSelectedItem'])) {
                                if ($inputParameters[$key]['maxSelectedItem'] == 1) {
                                    $option['multiple'] = false;
                                } else {
                                    $pluginOptions["maximumSelectionLength"] = $inputParameters[$key]['maxSelectedItem'];
                                    $option['multiple'] = true;
                                }
                            }

                            echo $form->field($model, $key)->widget(Select2::classname(), [
                                'data' => $valueParameters[$key],
                                'size' => Select2::MEDIUM,
                                'options' => $option,
                                'pluginOptions' => $pluginOptions,
                            ])->label($inputParameters[$key]['label']);
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }

}
