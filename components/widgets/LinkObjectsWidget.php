<?php

//******************************************************************************
//                                       LinkObjectsWidget.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 18 déc. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\widgets;

use \yii\web\JsExpression;
use yii\helpers\Html;
use Yii;
use \kartik\select2\Select2;

/**
 * Widget to manipulate and update lists of objects linked to a main object.
 * @see \kartik\select2\Select2
 * @author Morgane Vidal <morgane.vidal@inra.fr>, Vincent Migot <vincent.migot@inra.fr>
 */
class LinkObjectsWidget extends \yii\base\Widget {
    /**
     * The id of the div which will contains the widget view. 
     * It is auto-generated if null.
     * @var string
     */
    public $id;
    /**
     * The uri of the main item which will be linked to the others items.
     * @example http://www.phenome-fppn.fr/mauguio/diaphen/DIA2018-1
     * @var string
     */
    public $uri;
    /**
     * The ajax call link to update the links.
     * @example /phis-webapp/web/index.php?r=experiment%2Fupdate-variables 
     *              (corresponds to the value of Url::to(['experiment/update-variables'])
     * @var string
     */
    public $updateLinksAjaxCallUrl;
    /**
     * The items list (uri => label)
     * @example ["http://www.phenome-fppn.fr/diaphen/id/variables/v001" => "myVarName"]
     * @var array
     */
    public $items;
    /**
     * The list of the uris of the items linked to the object.
     * @example ["http://www.phenome-fppn.fr/diaphen/id/variables/v001",
     *           "http://www.phenome-fppn.fr/diaphen/id/variables/v002"]
     * @var array 
     */
    public $actualItems;
    /**
     * The item view string route
     * @example variable/view
     * @var string
     */
    public $itemViewRoute;
    /**
     * The concept label to display.
     * @example measured variables
     * @var string
     */
    public $conceptLabel;
    /**
     * True if the user can update the list, false if not.
     * Default value : false
     * @var boolean
     */
    public $canUpdate = false;
    /**
     * The info message displayed under the select2 form.
     * @var string
     */
    public $infoMessage;
    /**
     * The update message displayed on the select2.
     * @var string
     */
    public $updateMessage;
    
    const ITEM_SELECTOR_CLASS = "items-selector";
    const UPDATE_ITEMS_CLASS = "update-items";
    
    public function init() {
        parent::init();
        //Generates id if needed
        if ($this->id === null) {
            $this->id = uniqid();
        }
        // Check if the required params are not null
        if ($this->uri === null) {
            throw new \Exception("uri is not set");
        }
        if ($this->updateLinksAjaxCallUrl === null) {
            throw new \Exception("updateLinksAjaxCallUrl is not set");
        }
        if ($this->itemViewRoute === null) {
            throw new \Exception("itemViewRoute is not set");
        }
        if ($this->conceptLabel === null) {
            throw new \Exception("conceptLabel is not set");
        }
    }
    
    public function run() {
        $toReturn = "<div id=\"{$this->id}\">";
        
        //1. Generates the js code
        $toReturn .= $this->generatesAjaxCallJs();
        //2. Generates the html / js view code
        $toReturn .= $this->generatesViewCode();
        
        $toReturn .= "</div>";
        
        return $toReturn;
    }
    
    /**
     * Generates String corresponding to the javastring function to add the 
     * "eye" link to the item view.
     * @return String
     */
    private function getTemplateSelectionFunction() {
        $templateSelectionFunction[] = "function (obj) {";
        //Generate a map of itemUri => htmlLink
        $templateSelectionFunction[] = "var itemsLinks = {";
        foreach ($this->items as $uri => $label) {
            $templateSelectionFunction[] = "'" . $uri . "':'" . Html::a(
                "", 
                [$this->itemViewRoute, 'uri' => $uri],
                [
                    "class" => "fa fa-eye item-select-link",
                    "alt" => $label
                ]
            ) . "',";
        }
        $templateSelectionFunction[] = "};";
        // If the item id is present in the generated map return the text with the "eye" link to the corresponding view
        $templateSelectionFunction[] = "if (itemsLinks.hasOwnProperty(obj.id)) {";
        $templateSelectionFunction[] = "return obj.text + '&nbsp;' + itemsLinks[obj.id] + '&nbsp;'";
        $templateSelectionFunction[] = "} else {";
        // Otherwise return only the text (classic rendering)
        $templateSelectionFunction[] = "return obj.text";
        $templateSelectionFunction[] = "}";
        // close the function body
        $templateSelectionFunction[] = "}";
        // Join all lines in the constructed array to generate the function as a readable string
        $templateSelectionFunction = join("\n", $templateSelectionFunction);
        
        return $templateSelectionFunction;
    }
    
    /**
     * Generates the html/js code view for the select2 form.
     * @return string
     */
    private function generatesViewCode() {
        //Generates the html / js view code
        // Define the selection widget options with the formating javascript function created
        $widgetOptions = [
            'name' => LinkObjectsWidget::ITEM_SELECTOR_CLASS,
            'options' => [
                'multiple' => true
            ],
            'data' => $this->items,
            'value' => $this->actualItems,
            'pluginOptions' => [
                'templateSelection' => new JsExpression($this->getTemplateSelectionFunction()),
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'allowClear' => true
            ],
            'addon' => [
                'groupOptions' => [
                    'class' => LinkObjectsWidget::ITEM_SELECTOR_CLASS
                ]
            ]
        ];

        // Define specific options either user can update or not
        if ($this->canUpdate) {
            $widgetOptions['addon']['append'] = [
                'content' => Html::button('<i class="fa fa-check"></i>', [
                    'class' => 'btn btn-primary disabled ' . LinkObjectsWidget::UPDATE_ITEMS_CLASS,
                    'title' => $this->updateMessage,
                    'data-toogle' => 'tooltip',
                ]),
                'asButton' => true
            ];
        } else {
            $widgetOptions['disabled'] = true;
        }

        // Create widget HTML
        $toReturn = Select2::widget($widgetOptions);

        // Add the info box
        if ($this->canUpdate) {
            $toReturn .= '<p class="info-box">' . $this->infoMessage . '</p>';
        }
        
        return $toReturn;
    }
    
    /**
     * Generates the javascript to disable (or not) the update button 
     * and generates the ajax call to update the links. 
     * @return string
     */
    private function generatesAjaxCallJs() {
        $itemSelectorClass = LinkObjectsWidget::ITEM_SELECTOR_CLASS;
        $updateItemsClass = LinkObjectsWidget::UPDATE_ITEMS_CLASS;
        return <<<EOT
            <script>
            $(document).ready(function() {
                var originalList = $("#{$this->id} .{$itemSelectorClass} select").val();
                // On list change enable update button #{$this->id}
                $("#{$this->id} .{$itemSelectorClass} select").change(function() {
                    var currentList = $("#{$this->id} .{$itemSelectorClass} select").val();

                    // If current and original lists have the same length, they may be equals                    
                    if (currentList.length === originalList.length) {
                        var isEqual = true;
                        // Check if every item in current list exists in original list
                        for (var i in currentList) {
                            var item = currentList[i];

                            // As soon as a different item is found, set isEqual to false and exit loop
                            if (originalList.indexOf(item) === -1) {
                                isEqual = false;
                                break;
                            }
                        }

                        // If both list are equals, disable update button
                        if (isEqual) {
                            $("#{$this->id} .{$updateItemsClass}").addClass("disabled");                    
                        } else {
                        // Otherwise, enable update button
                            $("#{$this->id} .{$updateItemsClass}").removeClass("disabled");                    
                        }
                    } else {
                        // If current and original items list doesn't have same length, 
                        // they must be different, disable update button
                        $("#{$this->id} .{$updateItemsClass}").removeClass("disabled");                    
                    }
                });

                // On click
                $("#{$this->id} .{$updateItemsClass}").click(function() {
                    // If button is disabled exit from function
                    if ($(this).hasClass("disabled")) {
                        return;
                    }

                    // Build ajax parameter with the uri and the list of selected items
                    var ajaxParameters = {
                        uri: "{$this->uri}",
                        items: $("#{$this->id} .{$itemSelectorClass} select").val()
                    };

                    // Do the Ajax call
                    $.post(
                        "{$this->updateLinksAjaxCallUrl}",
                        ajaxParameters,
                        function(statusString) {
                            var statusArray = JSON.parse(statusString);

                            // Toastr options generated by @see https://codeseven.github.io/toastr/demo.html
                            toastr.options = {
                                "closeButton": false,
                                "debug": false,
                                "newestOnTop": false,
                                "progressBar": false,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "timeOut": "2000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            }

                            for(var i in statusArray) {
                                var status = statusArray[i];
                                if (status.exception.type === "Error") {
                                    toastr["error"](status.exception.details);
                                } else {
                                    toastr["success"](status.message);
                                    originalList = $("#{$this->id} .{$itemSelectorClass} select").val();
                                    $("#{$this->id} .{$updateItemsClass}").addClass("disabled");   
                                }
                            }
                        }
                    )
                })
            })
        </script>
EOT;
    }
}