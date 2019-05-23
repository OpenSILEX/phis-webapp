<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
      You wish to report a bug or to get help ?
      OpenSILEX development team can be contacted through the mailing list [opensilex-help@groupes.renater.fr](mailto:opensilex-help@groupes.renater.fr).
    </p>

    <p>
      You wish to get notified when new development are available ?
      You can follow us on <a href="https://twitter.com/OpenSilex"> twitter</a> or subscribe to one of the following mailing list :
      <ul>
        <li><a href="https://groupes.renater.fr/sympa/info/opensilex"> OpenSILEX</a>, for generic information about the latest news (training sessions, new releases, ...)</li>
        <li><a href="https://groupes.renater.fr/sympa/info/opensilex-èdev"> OpenSILEX-dev</a>, for technical information directed to OpenSILEX contributors</li>
    </p>

</div>
