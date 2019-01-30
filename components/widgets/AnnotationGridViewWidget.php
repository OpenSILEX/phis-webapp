<?php
//******************************************************************************
//                         AnnotationGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 23 Aug, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;
use app\models\yiiModels\YiiAnnotationModel;
use yii\grid\GridView;
use app\components\helpers\Vocabulary;
use kartik\icons\Icon;

/**
 * A widget used to generate a customizable annotation gridview interface
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class AnnotationGridViewWidget extends Widget {

    CONST ANNOTATIONS = "annotations";
    CONST NO_LINKED_ANNOTATIONS = "No linked Annotation(s)";
    CONST LINKED_ANNOTATIONS = "Linked Annotation(s)";
    
    /**
     * Define the annotations list which will be showed
     * @var mixed
     */
    public $annotations;

    public function init() {
        parent::init();
        // must be not null
        if ($this->annotations === null) {
            throw new \Exception("Annotations aren't set");
        }
    }

    /**
     * Render the annotation list
     * @return string the html string rendered
     */
    public function run() {
        if ($this->annotations->getCount() == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', self::NO_LINKED_ANNOTATIONS) . "</h3>";
        } else {
            $htmlRendered = "<h3>" . Yii::t('app', self::LINKED_ANNOTATIONS) . "</h3>";
            $htmlRendered .= GridView::widget([
                        'dataProvider' => $this->annotations,
                        'columns' => [
                            [
                                'label' => Yii::t('app',YiiAnnotationModel::COMMENTS_LABEL),
                                'attribute' => YiiAnnotationModel::COMMENTS,
                                'value' => function ($model) {
                                    return implode(('<br>,'), $model->comments);
                                }
                            ],
                            YiiAnnotationModel::CREATOR =>
                            [
                                'label' => Yii::t('app',YiiAnnotationModel::CREATOR_LABEL),
                                'attribute' => YiiAnnotationModel::CREATOR,
                                'value' => function($model) {
                                    return Vocabulary::prettyUri($model->creator);
                                },
                            ],
                            YiiAnnotationModel::MOTIVATED_BY => 
                            [
                                'label' => Yii::t('app',YiiAnnotationModel::MOTIVATED_BY_LABEL),
                                'attribute' => YiiAnnotationModel::MOTIVATED_BY,
                                'value' => function($model) {
                                    return Vocabulary::prettyUri($model->motivatedBy);
                                }
                            ],
                            [
                                'label' => Yii::t('app',YiiAnnotationModel::CREATION_DATE_LABEL),
                                'attribute' => YiiAnnotationModel::CREATION_DATE
                            ],
                            ['class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function($url, $model, $key) {
                                        return Html::a(Icon::show('eye-open', [], Icon::BSG), ['annotation/view', 'id' => $model->uri]);
                                    },
                                ]
                            ],
                        ],
            ]);
        }
        return $htmlRendered;
    }
}
