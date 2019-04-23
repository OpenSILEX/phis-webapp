<?php
//******************************************************************************
//                    ConcernedItemGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 23 Aug, 2018
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use yii\base\Widget;
use Yii;
use yii\grid\GridView;

/**
 * A widget used to generate a customisable concerned item GridView interface
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
abstract class ConcernedItemGridViewWidget extends Widget {

    CONST NO_CONCERNED_ITEMS_LABEL = "No items concerned";
    CONST CONCERNED_ITEMS_LABEL = "Concerned Items";
    CONST URI_LABEL = "URI";
    CONST RDF_TYPE_LABEL = "Type";
    CONST LABELS_LABEL = "Labels";
    
    /**
     * Concerned items list to show.
     * @var mixed
     */
    public $concernedItemsDataProvider;
    CONST CONCERNED_ITEMS_DATA_PROVIDER = "concernedItemsDataProvider";

    public function init() {
        parent::init();
        // must be not null
        if ($this->concernedItemsDataProvider === null) {
            throw new \Exception("Concerned items aren't set");
        }
    }

    /**
     * Render the concerned item list
     * @return string the HTML string rendered
     */
    public function run() {
        if ($this->concernedItemsDataProvider->getCount() == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', self::NO_CONCERNED_ITEMS_LABEL) . "</h3>";
        } else {
            $htmlRendered = "<h3>" . Yii::t('app', self::CONCERNED_ITEMS_LABEL) . "</h3>";
            $htmlRendered .= GridView::widget([
                        'dataProvider' => $this->concernedItemsDataProvider,
                        'columns' => $this->getColumns(),
            ]);
        }
        return $htmlRendered;
    }
    
    /**
     * Returns the columns of the GridView.
     * @return array
     */
    protected abstract function getColumns(): array;
}
