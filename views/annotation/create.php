<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Annotation $model
 */

$this->title = Yii::t(
    'app', 'Create an {modelClass}', [
    'modelClass' => 'Annotation',
    ]
);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Annotation')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <h1><?php echo Html::encode($this->title) ?></h1><br />

    <?php echo $this->render(
        '_form',
        [
            'model' => $model,
            'motivationIndividuals' => $motivationIndividuals,
        ]
    ) 
    ?>

</div>
