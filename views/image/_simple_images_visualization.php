<?php

//******************************************************************************
//                                       _simple_images_visualization.php
//
// Author(s): Julien Bonnefont <julien.bonnefont@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  15 juillet 2019
// Subject: visualization of images of a scientific object
//******************************************************************************

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $data array */
/* @var $model app\models\YiiImageModel */
?>

<div id="image-visualization " style="height:146px;">
    <?php
    if (isset($data) && !empty($data->getModels())) {
        //Preparation of the items array for the sortable widget
        $count = $imagesCount +count($data->getModels());
      
        echo '<div id="counterFragment" data-id=' . $count . ' ></div>';
    }
    ?>

    <ul id ="image-visualization-list-fragment" class="images" >
        <?php
        if (isset($data) && !empty($data->getModels())) {
            //Preparation of the items array for the sortable widget
            $count = $imagesCount;
            $first = true;
            foreach ($data->getModels() as $image) {
                $url = Url::to(['image/get', 'imageUri' => urlencode($image->uri)]);
                $obj = $model->concernedItems[0];

                if ($first && $count == 0) {
                    echo
                    '<li  ><a href="#lightbox" data-toggle="modal"  data-slide-to="0">' .
                    Html::img($url, [
                        'width' => 200,
                        'data-html' => 'true',
                        'title' => $obj,
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'bottom',
                    ]) .
                    '</a></li>';
                } else {
                    echo
                    '<li  ><a href="#lightbox" data-toggle="modal" data-slide-to= "' . $count . '">' .
                    Html::img($url, [
                        'width' => 200,
                        'data-html' => 'true',
                        'title' => $obj,
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'bottom',
                    ]) .
                    '</a></li>';
                }
                $first = false;
                $count += 1;
            }
        }
        ?>
    </ul>
    <ol id="carousel-indicators-fragment" class="carousel-indicators">
<?php
if (isset($data) && !empty($data->getModels())) {
    //Preparation of the items array for the sortable widget
    $count = $imagesCount;

    $first = true;
    foreach ($data->getModels() as $image) {

        if ($first && $count == 0) {
            echo
            '<li data-target="#lightbox" data-slide-to="0" class="active" ></li>';
        } else {
            echo
            '<li data-target="#lightbox" data-slide-to= "' . $count . '"  ></li>';
        }
        $first = false;
        $count += 1;
    }
}
?>

    </ol>
    <div id="carousel-inner-fragment" class="carousel-inner" role="listbox">
<?php
if (isset($data) && !empty($data->getModels())) {
    //Preparation of the items array for the sortable widget
    $first = true;
     $count = $imagesCount;
    foreach ($data->getModels() as $image) {
        $url = Url::to(['image/get', 'imageUri' => urlencode($image->uri)]);
        $obj = $model->concernedItems[0];
        if ($first && $count == 0) {
            echo
            ' <div class="item active ">' .
            Html::img($url, [
            ]) .
            '</div>';
        } else {
            echo
            ' <div class="item ">' .
            Html::img($url, [
            ]) .
            '</div>';
        }
        $first = false;
    }
}
?>

    </div>         


</div>