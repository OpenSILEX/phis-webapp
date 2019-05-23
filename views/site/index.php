<?php
use kartik\icons\Icon;
/* @var $this yii\web\View */
$this->title = Yii::$app->params['opensilex-webapp-type'];
?>
<div class="site-index">
    <div class="jumbotron">
        <?php
        if (Yii::$app->params['opensilex-webapp-type'] === 'phis') {
            ?>
            <img src="images/logos/phis_logo10.png" alt="logos_phis"/>
            <h3>You are on PHIS, the Hybrid Information System about Phenotyping !</h3>
            <?php
        } else {
            ?>
            <img src="images/logos/opensilex_logo_showcase-site.png" alt="logos_opensilex"/>
            <h3>You are on OpenSILEX, the Hybrid Information System about Life Science !</h3>
            <br />
            <div class="btns">
              <a href="https://twitter.com/OpenSilex"><?=  Icon::show('twitter', ['class' => 'fa-large'], Icon::FA) ?> Twitter</a>
              <a href="https://github.com/OpenSILEX"><?=  Icon::show('github', ['class' => 'fa-large'], Icon::FA) ?>Github</a>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
//    SILEX:info
//    If you want to activate animated multiple background images
//    feature uncomment these lines below and add
//    'use app\components\widgets\FullScreenImageSliderWidget;'
//    at the beginning of the file. You can also see full-slider.css
//    file to set different annimation style.
//    \SILEX:info
//        echo FullScreenImageSliderWidget::widget([
//            FullScreenImageSliderWidget::IMAGES_URL_LINK => [
//                "background/wallpaper_grapes_vine.jpg",
//                "background/wallpaper_leaf.jpg",
//                "background/wallpaper_tomato.jpg",
//                "background/wallpaper_vine.jpg"
//            ]
//        ]);
    ?>
</div>
