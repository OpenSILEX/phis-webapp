<?php
/* @var $this yii\web\View */
$this->title = 'PHIS';
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