<?php
//******************************************************************************
//                            EventGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 05 March, 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\grid\GridView;
use Yii;
use app\models\yiiModels\YiiEventModel;
use app\components\helpers\Vocabulary;
use kartik\icons\Icon;

/**
 * A widget used to generate a customizable concerned item GridView interface
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventGridViewWidget extends Widget {

    const EVENTS_LABEL = "Events";
    const NO_EVENT_LABEL = "No events";
    const EVENTS_ARE_NOT_SET_LABEL = "Concerned items aren't set";
    
    /**
     * Defines the list of events to show
     * @var mixed
     */
    public $eventsProvider;
    const EVENTS_PROVIDER = "eventsProvider";

    public function init() {
        parent::init();
        // must be not null
        if ($this->eventsProvider === null) {
            throw new \Exception(self::EVENTS_ARE_NOT_SET_LABEL);
        }
    }

    /**
     * Renders the list of the concerned items
     * @return string the HTML string rendered
     */
    public function run() {
        if ($this->eventsProvider->getCount() == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', self::NO_EVENT_LABEL) . "</h3>";
        } else {
            $htmlRendered = "<h3>" . Yii::t('app', self::EVENTS_LABEL) . "</h3>";
            $htmlRendered .= GridView::widget([
                'dataProvider' => $this->eventsProvider,
                'columns' => [
                    [
                        'label' => Yii::t('app', YiiEventModel::TYPE),
                        'attribute' => YiiEventModel::TYPE,
                        'value' => function ($model) {
                            return Vocabulary::prettyUri($model->rdfType);
                        }
                    ],
                    [
                        'attribute' => YiiEventModel::DATE
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function($url, $model, $key) {
                                return Html::a(Icon::show('eye-open', [], Icon::BSG), ['event/view', 'id' => $model->uri]);
                            },
                        ]
                    ],
                ],
                'options' => ['class' => "event-widget"]                    
            ]);
        }
        return $htmlRendered;
    }
}
