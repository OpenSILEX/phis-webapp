<?php

//******************************************************************************
//                                       ontology.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 10 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  10 avr. 2018
// Subject:
//******************************************************************************


use yii\helpers\Html;


$this->title = Yii::t('app', 'Vocabulary');
$this->params['breadcrumbs'][] = $this->title;
?>


<?php 
    $agronomicalObjects = [
        ['label' => 'Agronomical Object', 'uri' => 'http://www.phenome-fppn.fr/vocabulary/2017#AgronomicalObject'],
        ['label' => 'Plot', 'uri' => 'http://www.phenome-fppn.fr/vocabulary/2017#Plot'],
    ];
    
    $sensingDevices = [
        ["label" => "SensingDevice", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#SensingDevice"],
        ["label" => "Camera", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Camera"],
        ["label" => "Humidity Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#HumiditySensor"],
        ["label" => "Precipitation Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#PrecipitationSensor"],
        ["label" => "Radiation Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#RadiationSensor"],
        ["label" => "Spectroradiometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Spectroradiometer"],
        ["label" => "Temperature Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#TemperatureSensor"],
        ["label" => "Weighing Scale", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#WeighingScale"],
        ["label" => "Wind Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#WindSensor"],
    ];
    
    $cameras = [
            ["label" => "Hemispherical Camera", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#HemisphericalCamera"],
            ["label" => "Hyperspectral Camera", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#HyperspectralCamera"],
            ["label" => "RGB Camera", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#RGBCamera"],
        ];
    
    $humiditySensors = [
            ["label" => "Capacitance Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#CapacitanceSensor"],
            ["label" => "Electrical Resistance Block", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#ElectricalResistanceBlock"],
            ["label" => "Electrical Resistance Humidity Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#"],
            ["label" => "Tensiometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#ElectricalResistanceHumiditySensor"],
        ];
    
    $capacitanceSensors = [
           ["label" => "Capacitive Thin Film Polymer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#CapacitiveThinFilmPolymer"],
       ];

    $electricalResistanceBlocks = [
           ["label" => "Fiber Glass Block", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#FiberGlassBlock"],
           ["label" => "Gypsum Block", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#GypsumBlock"],
           ["label" => "Nylon Units", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#NylonUnits"],
       ];

     $precipitationSensors = [
          ["label" => "Level Measurement Rain Gauge", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#LevelMeasurementRainGauge"],
       ];

     $radiationSensors = [
           ["label" => "Net Radiometer Or Net Pyrradiometer Or Net Exchange Radiometer Or Balancemeter", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#NetRadiometerOrNetPyrradiometerOrNetExchangeRadiometerOrBalancemeter"],
           ["label" => "Pyranometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Pyranometer"],
           ["label" => "Pyranometer With Shade Ring", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#PyranometerWithShadeRing"],
           ["label" => "Quantum Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#QuantumSensor"],
           ["label" => "Surface Temperature Radiometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#SurfaceTemperatureRadiometer"],
       ];

       $spectroradiometers = [
           ["label" => "Green Seeker", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#GreenSeeker"],
       ];

       $temperatureSensors = [
           ["label" => "Electrical Resistance Thermometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#ElectricalResistanceThermometer"],
           ["label" => "Thermistor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Thermistor"],
           ["label" => "Thermocouple", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Thermocouple"],
       ];

       $windSensors = [
           ["label" => "CupAnemometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Tensiometer"],
           ["label" => "Propeller Anemometer", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#PropellerAnemometer"],
           ["label" => "Ultrasonic Wind Sensor", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#UltrasonicWindSensor"],
       ];

       $weighingScale = [
           ["label" => "Weighing Scale", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#WeighingScale"],
       ];

    $sensingDevicesNumber = count($cameras) + count($humiditySensors) + count($capacitanceSensors) 
            + count($electricalResistanceBlocks) + count($precipitationSensors) + count($radiationSensors)
            + count($spectroradiometers) + count($temperatureSensors) + count($sensingDevices)
            + count($windSensors);
    
    $images = [
           ["label" => "Image", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Image"],
           ["label" => "Hemispherical Image", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#HemisphericalImage"],
           ["label" => "Multispectral Image", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#MultispectralImage"],
           ["label" => "NIR Image", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#NIRImage"],
           ["label" => "RGB Image", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#RGBImage"],
           ["label" => "TIR Image", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#TIRImage"],
       ];
    
    
    $vectors = [
        ["label" => "Field Robot", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#FieldRobot"],
        ["label" => "Gantry", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#Gantry"],
        ["label" => "UAV", "uri" => "http://www.phenome-fppn.fr/vocabulary/2017#UAV"]
    ]
    
?>

<div class="site-ontology">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <h2><?= Html::encode(Yii::t('app', 'Summary')) ?></h2>
    
    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= Html::a(Yii::t('yii', 'Agronomical Objects'), ['ontology', '#' => 'agronomical-objects']) ?>
            <span class="badge badge-primary badge-pill"><?= Html::encode(count($agronomicalObjects)) ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= Html::a(Yii::t('yii', 'Sensing Devices'), ['ontology', '#' => 'sensing-devices']) ?>
            <!--<span class="badge badge-primary badge-pill"><?php // Html::encode($sensingDevicesNumber) ?></span>-->
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Cameras'), ['ontology', '#' => 'cameras']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($cameras)) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Humidity Sensors'), ['ontology', '#' => 'humidity-sensors']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($humiditySensors) + count($capacitanceSensors) + count($electricalResistanceBlocks)) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Precipitation Sensors'), ['ontology', '#' => 'precipitation-sensors']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($precipitationSensors)) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Radiation Sensors'), ['ontology', '#' => 'radiation-sensors']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($radiationSensors)) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Spectroradiometers'), ['ontology', '#' => 'spectroradiometers']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($spectroradiometers)) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Temperature Sensors'), ['ontology', '#' => 'temperature-sensors']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($temperatureSensors)) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Weighing Scales'), ['ontology', '#' => 'sensing-devices']) ?>
                    <!--<span class="badge badge-primary badge-pill"><?php // Html::encode(count($weighingScale)) ?></span>-->
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= Html::a(Yii::t('yii', 'Wind Sensors'), ['ontology', '#' => 'wind-sensors']) ?>
                    <span class="badge badge-primary badge-pill"><?= Html::encode(count($windSensors)) ?></span>
                </li>
            </ul>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= Html::a(Yii::t('yii', 'Images'), ['ontology', '#' => 'images']) ?>
            <span class="badge badge-primary badge-pill"><?= Html::encode(count($images)) ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= Html::a(Yii::t('yii', 'Vectors'), ['ontology', '#' => 'vectors']) ?>
            <span class="badge badge-primary badge-pill"><?= Html::encode(count($vectors)) ?></span>
        </li>
    </ul>
    

    <h2 id="species"><?= Html::encode(Yii::t('app', 'Species')) ?></h2>
        <?php    
        $speciesProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $species,
           'sort' => [
               'attributes' => ['label_la', 'label_en', 'label_fr', 'uri']
           ]
        ]);

        echo yii\grid\GridView::widget([
            'dataProvider' => $speciesProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label_la',
                'label_en',
                'label_fr',
                'uri'
            ]
        ]);

        ?>
    
    <h2 id="agronomical-objects"><?= Html::encode(Yii::t('app', 'Agronomical Objects')) ?></h2>
    
        <?php
        $agronomicalObjectsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $agronomicalObjects,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);

        echo yii\grid\GridView::widget([
            'dataProvider' => $agronomicalObjectsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);
        ?>

    <h2 id="sensing-devices"><?= Html::encode(Yii::t('app', 'Sensing Devices')) ?></h2>
    <?php
        $sensingDevicesProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $sensingDevices,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $sensingDevicesProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    
    <h3 id="cameras"><?= Html::encode(Yii::t('app', 'Cameras')) ?></h3>
    <?php
        $camerasProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $cameras,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $camerasProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h3 id="humidity-sensors"><?= Html::encode(Yii::t('app', 'Humidity Sensors')) ?></h3>
    <?php
        $humiditySensorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $humiditySensors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $humiditySensorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
     <h4 id="capacitance-sensors"><?= Html::encode(Yii::t('app', 'Capacitance Sensors')) ?></h4>
     <?php
        $capacitanceSensorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $capacitanceSensors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $capacitanceSensorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
     
     <h4 id="electrical-resistance-blocks"><?= Html::encode(Yii::t('app', 'Electrical Resistance Blocks')) ?></h4>
     <?php
        $electricalResistanceBlocksProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $electricalResistanceBlocks,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $electricalResistanceBlocksProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
     ?>
     
    <h3 id="precipitation-sensors"><?= Html::encode(Yii::t('app', 'Precipitation Sensors')) ?></h3>
    <?php
        $precipitationSensorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $precipitationSensors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $precipitationSensorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h3 id="radiation-sensors"><?= Html::encode(Yii::t('app', 'Radiation Sensors')) ?></h3>
    <?php
        $radiationSensorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $radiationSensors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $radiationSensorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h3 id="spectroradiometers"><?= Html::encode(Yii::t('app', 'Spectroradiometers')) ?></h3>
    <?php
        $spectroradiometersProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $spectroradiometers,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $spectroradiometersProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h3 id="temperature-sensors"><?= Html::encode(Yii::t('app', 'Temperature Sensors')) ?></h3>
    <?php
        $temperatureSensorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $temperatureSensors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $temperatureSensorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h3 id="wind-sensors"><?= Html::encode(Yii::t('app', 'Wind Sensors')) ?></h3>
    <?php
        $windSensorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $windSensors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $windSensorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h2 id="vectors"><?= Html::encode(Yii::t('app', 'Vectors')) ?></h2>
    <?php
        $vectorsProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $vectors,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $vectorsProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
    
    <h2 id="images"><?= Html::encode(Yii::t('app', 'Images')) ?></h2>
    <?php
        $imagesProvider = new \yii\data\ArrayDataProvider([
           'allModels' => $images,
           'sort' => [
               'attributes' => ['label', 'uri']
           ]
        ]);        

        echo yii\grid\GridView::widget([
            'dataProvider' => $imagesProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'label',
                'uri'
            ]
        ]);   
    ?>
</div>
