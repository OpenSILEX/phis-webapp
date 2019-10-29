<?php

//******************************************************************************
//                            EventGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 Mar. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\widgets\event;

use yii\base\Widget;
use yii\helpers\Html;
use yii\grid\GridView;
use Yii;
use app\models\yiiModels\YiiEventModel;
use app\components\helpers\Vocabulary;
use kartik\icons\Icon;

/**
 * Detail Event GridView widget.
 * @author Julien Bonnefont <julien.bonnefont@inra.fr>
 */
class DetailEventGridViewWidget extends Widget {

    const EVENTS_LABEL = "Events";
    const NO_EVENT_LABEL = "No events";
    const EVENTS_ARE_NOT_SET_LABEL = "Events aren't set";
    const HTML_CLASS = "event-widget";

    /**
     * Defines the list of events to show.
     * @var mixed
     */
    public $dataProvider;

    const DATA_PROVIDER = "dataProvider";

    public function init() {
        parent::init();
        // must be not null
        if ($this->dataProvider === null) {
            throw new \Exception(self::EVENTS_ARE_NOT_SET_LABEL);
        }
    }

    /**
     * Renders the list of the concerned items.
     * @return string the HTML string rendered
     */
    public function run() {
        if ($this->dataProvider->getCount() == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', self::NO_EVENT_LABEL) . "</h3>";
        } else {
            $htmlRendered = "<h3>" . Yii::t('app', self::EVENTS_LABEL) . "</h3>";
            $htmlRendered .= GridView::widget([
                        'dataProvider' => $this->dataProvider,
                        'columns' => [
                            [
                                'label' => Yii::t('app', YiiEventModel::TYPE),
                                'attribute' => YiiEventModel::TYPE,
                                'value' => function ($model) {
                                    return explode("#", $model->rdfType)[1];
                                }
                            ],
                            [
                                'label' => Yii::t('app', YiiEventModel::ANNOTATIONS),
                                'attribute' => YiiEventModel::ANNOTATIONS,
                                'format' => 'html',
                                'value' => function ($model) {

                                    $annotations = $model->annotations;
                                   
                                    $toReturn = '';
                                    $marginLeft = 0;
                                    foreach ($annotations as $annotation) {

                                        $toReturn .= '<div class="well well-lg" style="margin:0px 0px 5px ' . $marginLeft . 'px;">';
                                        foreach ($annotation['bodyValues'] as $i => $value) {
                                            $toReturn .= $value . ' ';
                                        }
                                        $marginLeft += 10;
                                        $toReturn .= '<div class="pull-right">';
                                        $toReturn .= date('d/m/Y H:i', strtotime($annotation['creationDate']));
                                        $toReturn .= '</div></div>';
                                    }
                                    return $toReturn;
                                }
                            ],
                            [
                                'format' => ['date', 'php:d/m/Y H:i'],
                                'attribute' => YiiEventModel::DATE
                            ],
                            ['class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function($url, $model, $key) {
                                        return Html::a(
                                                        Icon::show('eye-open', [], Icon::BSG), ['event/view', 'id' => $model->uri],['target'=>'_blank','class' => 'target-blank']);
                                    },
                                ]
                            ],
                        ],
                        'options' => ['class' => self::HTML_CLASS]
            ]);
        }
        return $htmlRendered;
    }

}
