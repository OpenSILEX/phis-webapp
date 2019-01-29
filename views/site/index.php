<?php
/* @var $this yii\web\View */
$this->title = 'PHIS';
?>
<div class="site-index">
    <div class="jumbotron">
        <img src="images/logos/phis_logo10.png" alt="logos_phis"/>
        <h3><?= Yii::t('app/messages', 'You are on PHIS, the Hybrid Information System about Phenotyping !') ?></h3>
        <!--<h3>Yii::t('app/messages', 'Intro'); </h3>-->
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