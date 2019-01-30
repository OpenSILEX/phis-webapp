<?php

//**********************************************************************************************
//                                       view.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: August 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  August, 30 2017
// Subject: implements the view page for a Layer
//***********************************************************************************************
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\YiiLayerModel */

$this->title = $model->objectURI;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['experiment/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['experiment/view', 'id' => $this->title]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Map Visualization');
?>

<link rel="stylesheet" type="text/css" href="../library/olv4.3.2/ol.css">
<script src="../library/olv4.3.2/ol.js"></script>
    
<div class="layer-view">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>
        <?php if (Yii::$app->session['isAdmin']) {
           echo Html::a(Yii::t('app', 'Generate Map'), 
               ['layer/view', 'objectURI' => $model->objectURI, 'objectType' => Yii::$app->params["Experiment"], 'depth' => 'true', 'generateFile' => 'true'], ['class' => 'btn btn-success']);
           }
        ?>
     </h1>
    <div id="map" class="map"></div>
    <p><i>Use Alt+Shift+Drag to rotate the map. Use Ctrl+Click+Drag to select multiple elements.</i></p>
    <div id="info">
        <div id="visualization-dataset">
            
        </div>
        <div id="visualization-images">
            
        </div>
        <div id="agronomical-objects-infos"></div>
    </div>
<!--  SILEX:todo une fois les tests styles map finis, nettoyer et ranger le css avec le css etc. \SILEX:todo-->
    <style>
      .ol-dragbox {
        background-color: rgba(255,255,255,0.4);
        border-color: rgba(100,150,0,1);
      }
    </style>
    <script> //MAP Visualisation
            var vectorSource = new ol.source.Vector({
                url:'<?= $model->filePath ?>',
                format: new ol.format.GeoJSON(),
                wrapX: false
              });

            var vectorLayer = new ol.layer.Vector({
              source: vectorSource
            });
            
            //Pour recentrer par défaut sur les objets à visualiser
            vectorSource.once('change', function(evt){
              if (vectorSource.getState() === 'ready') {
                // now the source is fully loaded
                if (vectorLayer.getSource().getFeatures().length > 0) {
                  map.getView().fit(vectorSource.getExtent(), map.getSize());
                  console.info(map.getView().getCenter());
                  console.info(map.getView().getZoom());
                }
              }
            });
            
            var map = new ol.Map({
              layers: [
                        new ol.layer.Tile({
                            source: new ol.source.OSM()
                        }),
                        vectorLayer
                      ],
              target: 'map',
              view: new ol.View({
                center: [0,0],
                zoom: 15
              })
            });
            
            //Interractions avec la carte 
            //Une interaction normale, au click sur un élément
            var select = new ol.interaction.Select();
            map.addInteraction(select);
            
            var selectedFeatures = select.getFeatures();
            
            //Une interaction via dragbox (selection de plusieurs éléments)
            var dragBox = new ol.interaction.DragBox({
                condition: ol.events.condition.platformModifierKeyOnly
            });

            map.addInteraction(dragBox);

             dragBox.on('boxend', function() {
                // features that intersect the box are added to the collection of
                // selected features
                var extent = dragBox.getGeometry().getExtent();
                vectorSource.forEachFeatureIntersectingExtent(extent, function(feature) {
                  selectedFeatures.push(feature);
               });
            });
            
             // clear selection when drawing a new box and when clicking on the map
            dragBox.on('boxstart', function() {
              selectedFeatures.clear();
            });
            
            var infoBox = document.getElementById('agronomical-objects-infos');
            var selectedPlots;
            
            selectedFeatures.on(['add', 'remove'], function() {
                selectedPlots = selectedFeatures.getArray().map(function(feature) {
                    //Tableau représentant un plot 
                    //[uri, typeElement, alias, species, variety, genotype, experimentModalities, replication]
                    var plot = [];
                    if (feature.get('uri') !== undefined) {
                        plot.push(feature.get('uri'));
                        if (feature.get('typeElement') !== undefined) {
                            plot.push(feature.get('typeElement'));
                        } else {
                            plot.push("");
                        }
                        if (feature.get('label') !== undefined) {
                            plot.push(feature.get('label'));
                        } else {
                            plot.push("");
                        }
                        if (feature.get('species') !== undefined) {
                            var species = feature.get('species').split("species/");
                            plot.push(species[1]);
                        } else {
                            plot.push("");
                        }
                        if (feature.get('variety') !== undefined) {
                            var variety = feature.get('variety').split("v/");
                            plot.push(variety[1]);
                        } else {
                            plot.push("");
                        }
                        if (feature.get('genotype') !== undefined) {
                            var genotype = feature.get('genotype').split("#");
                            plot.push(genotype[1]);
                        } else {
                            plot.push("");
                        }
                        if (feature.get('experimentModalities') !== undefined) {
                            plot.push(feature.get('experimentModalities'));
                        } else {
                            plot.push("");
                        }
                        if (feature.get('replication') !== undefined) {
                            plot.push(feature.get('replication'));
                        } else {
                            plot.push("");
                        }
                    }
                    
                    return plot;
                });
                //SILEX:todo 
                //Générer le tableau et l'affichage plus proprement
                if (selectedPlots.length > 0) {
                                     
                   var infoBoxHTML = "<table class=\"table\">"
                                            + "<thead>"
                                                + "<tr>"
                                                    + "<th><?= Yii::t('app', 'Alias')?></th>"
                                                    + "<th><?= Yii::t('app', 'Crop Species') ?></th>"
                                                    + "<th><?= Yii::t('app', 'Variety') ?></th>"
                                                    + "<th><?= Yii::t('app', 'Experiment Modalities') ?></th>"
                                                    + "<th><?= Yii::t('app', 'Replication') ?></th>"
                                                + "</tr>"
                                            + "</thead>"
                                            + "<tbody>";
                    for (var i = 0; i < selectedPlots.length; i++) {
                        infoBoxHTML += "<tr>"
                                                + "<th scope=\"row\">" + selectedPlots[i][2] + "</th>"
                                                + "<td>" + selectedPlots[i][3] + "</td>"
                                                + "<td>" + selectedPlots[i][4] + "</td>"
                                                + "<td>" + selectedPlots[i][6] + "</td>"
                                                + "<td>" + selectedPlots[i][7] + "</td>"
                                            + "</tr>";
                    }
                    
                    infoBoxHTML += "</tbody>"
                    infoBox.innerHTML = infoBoxHTML;
                                     + "</table>";                  
                } else {
                  infoBox.innerHTML = " <?= Yii::t('app/messages', 'No plot selected')?> ";
                }
                //\SILEX:todo
            });
            
             $(document).ready(function(){
                $('#visualization-dataset').load('<?php echo Url::to(['dataset/search-from-layer']) ?>');
                $('#visualization-images').load('<?php echo Url::to(['image/search-from-layer'])?>');
             });
             
            $(document).on('click', '#datasetSearchButton', function() {
                            //SILEX:info
                            //This is a quick fix which will be removed when the datasets 
                            //functionnality will be updated
                            //\SILEX:info
                            if (selectedPlots.length > 20) { 
                                alert("Too many plots selected.");
                            } else {
                                var searchFormData = new FormData();
                                var data = $('form').serializeArray();
                                $.each(data, function(key, input) {
                                   searchFormData.append(input.name, input.value); 
                                });
                                var plots = "";
                                if (typeof selectedPlots !== 'undefined') {
                                    for (var i = 0; i < selectedPlots.length; i++) {
                                        plots += selectedPlots[i][0];
                                        if (i < selectedPlots.length-1) {
                                            plots += ",";
                                        } 
                                    }
                                } else {
                                    plots = null;
                                }
                            }
                            searchFormData.append("agronomicalObjects", plots);

                            $.ajax({
                               url: '<?php echo Url::to(['dataset/search-from-layer']) ?>', 
                               type: 'POST',
                               processData: false,
                               datatype: 'json',
                               contentType: false,
                               data: searchFormData 
                            }) 
                              .done(function (data) {
                                //SILEX:todo
                                //gestion messages d'erreur
                                //\SILEX:todo          
                                $('#visualization-dataset').html(data);
                             })
                             .fail(function (jqXHR, textStatus) {
                                //SILEX:todo
                                //gestion messages d'erreur
                                //\SILEX:todo
                                alert("ERROR : " + jqXHR);
                             });    
                        });
                        
            $(document).on('click', '#imageSearchButton', function() {
                var searchFormData = new FormData();
                var data = $('form').serializeArray();
                $.each(data, function(key, input) {
                    searchFormData.append(input.name, input.value); 
                });
                var plots = "";
                if (typeof selectedPlots !== 'undefined') {
                    for (var i = 0; i < selectedPlots.length; i++) {
                        plots += selectedPlots[i][0];
                        if (i < selectedPlots.length-1) {
                            plots += ",";
                        } 
                    }
                } else {
                    plots = null;
                }
                
                searchFormData.append("concernedItems", plots);
                
                $.ajax({
                    url: '<?php echo Url::to(['image/search-from-layer']) ?>', 
                    type: 'POST',
                    processData: false,
                    datatype: 'json',
                    contentType: false,
                    data: searchFormData 
                 }) 
                   .done(function (data) {
                     //SILEX:todo
                     //gestion messages d'erreur
                     //\SILEX:todo
                     $('#visualization-images').html(data);
                  })
                  .fail(function (jqXHR, textStatus) {
                     //SILEX:todo
                     //gestion messages d'erreur
                     //\SILEX:todo
                     alert("ERROR : " + jqXHR);
                  });
            });
            
    </script>    
</div>
