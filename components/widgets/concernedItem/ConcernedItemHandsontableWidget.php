<?php
//******************************************************************************
//                    ConcernedItemHandsontableWidget.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\concernedItem;

use yii\base\Widget;
use Yii;
use app\models\yiiModels\YiiConcernedItemModel;

/**
 * Concerned item GridView widget.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class ConcernedItemHandsontableWidget extends Widget {

    const NO_CONCERNED_ITEMS_LABEL = "No items concerned";
    const CONCERNED_ITEMS_LABEL = "Concerned Items";
    const CONCERNED_ITEMS_URI_COLUMN_HEADER = "URI";
    const CONCERNED_ITEMS_URI_COLUMN_PLACEHOLDER 
    = "http://www.opensilex.org/example/2019/o19000001";
    
    const HTML_WIDGET_CLASS = "concerned-item-widget";
    const HTML_DIV_FORM_GROUP_CLASS = "form-group";
    const HTML_DIV_FIELD_CLASS_PREFIXE = "field-";
    const HTML_LABEL_CLASS = "control-label";
    const HTML_HANDSONTABLE_ID = "handsontable";
    
    private $htmlDivFieldClass;
    
    /**
     * Concerned items list to show.
     * @var mixed
     */
    public $dataProvider;
    const DATA_PROVIDER = "dataProvider";
    
    public $inputModelClass;
    const INPUT_MODEL_CLASS = "inputModelClass";
    
    public $inputModelConcernedItemsUrisAttributeName;
    const INPUT_MODEL_CONCERNED_ITEMS_URIS_ATTRIBUTE_NAME = "inputModelConcernedItemsUrisAttributeName";

    public function init() {
        parent::init();
        
        $this->htmlDivFieldClass = self::HTML_DIV_FIELD_CLASS_PREFIXE . $this->inputModelClass 
                . "-" . $this->inputModelConcernedItemsUrisAttributeName;
    }

    /**
     * Renders the concerned item list
     * @return string the HTML string rendered
     */
    public function run() {
        return 
            "<div class=\"" . self::HTML_DIV_FORM_GROUP_CLASS . " " . $this->htmlDivFieldClass . "\">"
          .   "<label class=\"" . self::HTML_LABEL_CLASS . "\">" . Yii::t('app', self::CONCERNED_ITEMS_LABEL) . "</label>"
          .   "<div id=\"objects-creation\">"
          .       "<div id=\"handsontable\"></div>"
          .   "</div>"
          .   "<div id=\"loader\" class=\"loader\" style=\"display:none\"></div>"
          .       "<script type = \"text/javascript\" src = \"../components/widgets/concernedItem/concernedItemHandsontableCreation.js\">"
          .       "</script>"
          .       "<script type=\"text/javascript\">"
          .           "CONCERNED_ITEM_HANDSONTABLE_CREATION.init(["
          .               "\"" . self::HTML_HANDSONTABLE_ID . "\","
          .               "\"" . Yii::t("app", self::CONCERNED_ITEMS_URI_COLUMN_HEADER) . "\","
          .               "\"" . Yii::t("app", self::CONCERNED_ITEMS_URI_COLUMN_PLACEHOLDER) . "\","
          .           "]);"
          .       "</script>"
          . "</div>";
    }
}
