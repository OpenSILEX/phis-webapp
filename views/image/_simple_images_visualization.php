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

<div class="image-visualization " style="height:146px;">


    <ul class="images" >
        <?php
        if (isset($data) && !empty($data->getModels())) {
            //Preparation of the items array for the sortable widget

            foreach ($data->getModels() as $image) {
                $url = Url::to(['image/get', 'imageUri' => urlencode($image->uri)]);
                echo
                '<li >' .
                Html::img($url, [
                    'width' => 200,
                    'onclick' => 'showImage("' . $url . '")'
                ]) .
              '</li>';
            }
        } else {
            echo "<br><div class='alert alert-info' id='scientific-object-data-visualization-alert-div' role='alert-info'>
                    <p>You have to click on data to see images</p>   </div>";
            
        }
        ?>
    </ul>
    <!--Image modal-->
    <div class="modal " id="modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <!--<h4 class="modal-title"></h4>-->
                </div>
                <div class="modal-body">
                    <img src=""alt="" id="modalImage" class="img-responsive">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script> //Modal show 
        function showImage(imagePath) {
            $('#modalImage').attr("src", imagePath);
            $('#modal').modal({show: true});
        }
    </script>

</div>