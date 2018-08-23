<?php
//******************************************************************************
//                         AnnotationWidget.php
// SILEX-PHIS
// Copyright © INRA  Arnaud Charleroy <arnaud.charleroy@inra.fr>
// Creation date: 10 july 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;
use app\models\yiiModels\YiiAnnotationModel;
use kartik\icons\Icon;

/**
 *  A widget used to generate a customizable annotation interface button
 */
class AnnotationButtonWidget extends Widget {

    CONST ADD_ANNOTATION_LABEL = 'Add an annotation';
    /**
     * Define the model which will be annoted
     * @var mixed
     */
    public $targets;

    const TARGETS = "targets";

    public function init() {
        parent::init();
        // must be not null
        if ($this->targets === null) {
           throw new \Exception("Targets aren't set");
        }
         // must be an array
        if (!is_array($this->targets)) {
          throw new \Exception("Targets list is not an array");
        }
         // must contains at least one element
        if (empty($this->targets)) {
            throw new \Exception("Targets list is empty");
        }
    }

    /**
     * Render the annotation button
     * @return string the string rendered
     */
    public function run() {
        //SILEX:conception
        // Maybe create a widget bar and put buttons in it to use the same style
        //\SILEX:conception
        return Html::a(
                    Icon::show('comment', [], Icon::FA) . " " . Yii::t('app', self::ADD_ANNOTATION_LABEL),
                    [
                        'annotation/create',
                        YiiAnnotationModel::TARGETS => $this->targets,
                    ], 
                    [
                    'class' => 'btn btn-default',
                    ] 
                );
    }

}
