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


    <ul id ="image-visualization-list-fragment" class="images" >
        <?php
        if (isset($data) && !empty($data->getModels())) {
            //Preparation of the items array for the sortable widget

            foreach ($data->getModels() as $image) {
                $url = Url::to(['image/get', 'imageUri' => urlencode($image->uri)]);
                $obj = $model->concernedItems[0];
                echo
                '<li  ><a href="#lightbox" data-toggle="modal">' .
                Html::img($url, [
                    'width' => 200,
                    //  'onclick' => 'showImage()',
                    'data-html' => 'true',
                    'title' => $obj,
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'bottom',
                ]) .
                '</a></li>';
            }
        }
        ?>
    </ul>
    <ol id="carousel-indicators-fragment" class="carousel-indicators">
        <?php
        if (isset($data) && !empty($data->getModels())) {
            //Preparation of the items array for the sortable widget
            $count = $imagesCount;
            foreach ($data->getModels() as $image) {
                $first = true;
                if ($first) {
                    echo
                    '<li data-target="#carousel-example-generic" data-slide-to="0" class="active" ></li>';
                } else {
                    '<li data-target="#carousel-example-generic" data-slide-to= "' . $count . '"  ></li>';
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
            foreach ($data->getModels() as $image) {
                $url = Url::to(['image/get', 'imageUri' => urlencode($image->uri)]);
                $obj = $model->concernedItems[0];
                if ($first) {
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