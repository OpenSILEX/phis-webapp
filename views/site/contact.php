<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = Yii::t('app', 'Contact / Help');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
      <?= Yii::t('app/messages', 'You wish to report a bug or to get help ? OpenSILEX development team can be contacted through the email address') ?>
      <a href="mailto:opensilex-help@groupes.renater.fr"> opensilex-help@groupes.renater.fr</a>.
    </p>

    <p>
      <?= Yii::t('app/messages', 'You wish to get notified when new developments are available ? You can follow us on') ?>
      <a href="https://twitter.com/OpenSilex" target="_blank"> <?= Yii::t('app', 'twitter') ?></a>
      <?= Yii::t('app/messages', 'or subscribe to one of the following mailing list:') ?>
      <ul>
        <li><a href="https://groupes.renater.fr/sympa/subscribe/opensilex" target="_blank"> OpenSILEX</a>,
          <?= Yii::t('app/messages', 'for generic information about the latest news (training sessions, new releases, ...)') ?></li>
        <li><a href="https://groupes.renater.fr/sympa/subscribe/opensilex-dev" target="_blank"> OpenSILEX-dev</a>,
          <?= Yii::t('app/messages', 'for technical information directed to OpenSILEX contributors') ?></li>
    </p>

</div>
