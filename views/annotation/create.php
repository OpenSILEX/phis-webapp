<?php
//******************************************************************************
//                           create.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 6 Aug, 2017
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use app\controllers\AnnotationController;
use app\models\yiiModels\YiiAnnotationModel;

/**
 * @var yii\web\View $this
 * @var app\models\yiiModels\YiiAnnotationModel $model
 * Implements the create page for an annotation
 */

$this->title = Yii::t(
    'app',
    YiiAnnotationModel::ADD_ANNOTATION,
    //'Create an {modelClass}', [
    //'modelClass' => YiiAnnotationModel::LABEL,
    //],
  ['n' => 1]
);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Annotation')];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="annotation-create">

    <h1><?php echo Html::encode($this->title) ?></h1><br />

    <?php echo $this->render(
        '_form',
        [
            'model' => $model,
            AnnotationController::MOTIVATION_INSTANCES => ${AnnotationController::MOTIVATION_INSTANCES},
        ]
    )
    ?>

</div>
