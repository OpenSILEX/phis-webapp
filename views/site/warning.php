<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-warning">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-warning">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above warning occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
