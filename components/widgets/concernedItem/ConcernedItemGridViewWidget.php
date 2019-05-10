<?php
//******************************************************************************
//                    ConcernedItemGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 23 Aug. 2018
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\concernedItem;

use yii\base\Widget;
use Yii;
use yii\grid\GridView;
use app\models\yiiModels\YiiConcernedItemModel;
use app\components\helpers\Vocabulary;

/**
 * Concerned item GridView widget.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class ConcernedItemGridViewWidget extends Widget {

    const NO_CONCERNED_ITEMS_LABEL = "No items concerned";
    const CONCERNED_ITEMS_LABEL = "Concerned Items";
    
    const HTML_CLASS = "concerned-item-widget";
    
    /**
     * Concerned items list to show.
     * @var mixed
     */
    public $dataProvider;
    const DATA_PROVIDER = "dataProvider";

    public function init() {
        parent::init();
        // must be not null
        if ($this->dataProvider === null) {
            throw new \Exception("Concerned items aren't set");
        }
    }

    /**
     * Renders the concerned item list.
     * @return string the HTML string rendered
     */
    public function run() {
        if ($this->dataProvider->getCount() == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', YiiConcernedItemModel::URI_LABEL) . "</h3>";
        } else {
            $htmlRendered = "<h3>" . Yii::t('app', self::CONCERNED_ITEMS_LABEL) . "</h3>";
            $htmlRendered .= GridView::widget([
                'dataProvider' => $this->dataProvider,
                'columns' => [
                    [
                        'label' => Yii::t('app', YiiConcernedItemModel::URI_LABEL),
                        'attribute' => YiiConcernedItemModel::URI,
                        'value' => function ($model) {
                            return Vocabulary::prettyUri($model->uri);
                        }
                    ],
                    YiiConcernedItemModel::RDF_TYPE =>
                    [
                        'label' => Yii::t('app', YiiConcernedItemModel::RDF_TYPE_LABEL),
                        'attribute' => YiiConcernedItemModel::RDF_TYPE,
                        'value' => function($model) {
                            return Vocabulary::prettyUri($model->rdfType);
                        },
                    ],
                    YiiConcernedItemModel::LABELS => 
                    [
                        'label' => Yii::t('app', YiiConcernedItemModel::LABELS_LABEL),
                        'attribute' => YiiConcernedItemModel::LABELS,
                        'value' => function($model) {
                            return implode((', '), $model->labels);
                        }
                    ]
                ],
                'options' => ['class' => self::HTML_CLASS]     
            ]);
        }
        return $htmlRendered;
    }
}
