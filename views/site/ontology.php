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
        ['label' => 'Scientific Object', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#ScientificObject'],
        ['label' => 'Plot', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Plot'],
        ['label' => 'Plant', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Plant'],
        ['label' => 'Leaf', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Leaf'],
        ['label' => 'Rootstock', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Rootstock'],
        ['label' => 'Scion', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Scion'],
        ['label' => 'Seed', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Seed'],
        ['label' => 'Silk', 'uri' => 'http://www.opensilex.org/vocabulary/oeso#Silk'],
    ];
    
    $sensingDevices = [
        ["label" => "SensingDevice", "uri" => "http://www.opensilex.org/vocabulary/oeso#SensingDevice"],
        ["label" => "Camera", "uri" => "http://www.opensilex.org/vocabulary/oeso#Camera"],
        ["label" => "Humidity Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#HumiditySensor"],
        ["label" => "Precipitation Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#PrecipitationSensor"],
        ["label" => "Radiation Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#RadiationSensor"],
        ["label" => "Spectroradiometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#Spectroradiometer"],
        ["label" => "Temperature Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#TemperatureSensor"],
        ["label" => "Weighing Scale", "uri" => "http://www.opensilex.org/vocabulary/oeso#WeighingScale"],
        ["label" => "Wind Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#WindSensor"],
    ];
    
    $cameras = [
            ["label" => "Hemispherical Camera", "uri" => "http://www.opensilex.org/vocabulary/oeso#HemisphericalCamera"],
            ["label" => "Hyperspectral Camera", "uri" => "http://www.opensilex.org/vocabulary/oeso#HyperspectralCamera"],
            ["label" => "RGB Camera", "uri" => "http://www.opensilex.org/vocabulary/oeso#RGBCamera"],
        ];
    
    $humiditySensors = [
            ["label" => "Capacitance Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#CapacitanceSensor"],
            ["label" => "Electrical Resistance Block", "uri" => "http://www.opensilex.org/vocabulary/oeso#ElectricalResistanceBlock"],
            ["label" => "Electrical Resistance Humidity Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#"],
            ["label" => "Tensiometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#ElectricalResistanceHumiditySensor"],
        ];
    
    $capacitanceSensors = [
           ["label" => "Capacitive Thin Film Polymer", "uri" => "http://www.opensilex.org/vocabulary/oeso#CapacitiveThinFilmPolymer"],
       ];

    $electricalResistanceBlocks = [
           ["label" => "Fiber Glass Block", "uri" => "http://www.opensilex.org/vocabulary/oeso#FiberGlassBlock"],
           ["label" => "Gypsum Block", "uri" => "http://www.opensilex.org/vocabulary/oeso#GypsumBlock"],
           ["label" => "Nylon Units", "uri" => "http://www.opensilex.org/vocabulary/oeso#NylonUnits"],
       ];

     $precipitationSensors = [
          ["label" => "Level Measurement Rain Gauge", "uri" => "http://www.opensilex.org/vocabulary/oeso#LevelMeasurementRainGauge"],
       ];

     $radiationSensors = [
           ["label" => "Net Radiometer Or Net Pyrradiometer Or Net Exchange Radiometer Or Balancemeter", "uri" => "http://www.opensilex.org/vocabulary/oeso#NetRadiometerOrNetPyrradiometerOrNetExchangeRadiometerOrBalancemeter"],
           ["label" => "Pyranometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#Pyranometer"],
           ["label" => "Pyranometer With Shade Ring", "uri" => "http://www.opensilex.org/vocabulary/oeso#PyranometerWithShadeRing"],
           ["label" => "Quantum Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#QuantumSensor"],
           ["label" => "Surface Temperature Radiometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#SurfaceTemperatureRadiometer"],
       ];

       $spectroradiometers = [
           ["label" => "Green Seeker", "uri" => "http://www.opensilex.org/vocabulary/oeso#GreenSeeker"],
       ];

       $temperatureSensors = [
           ["label" => "Electrical Resistance Thermometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#ElectricalResistanceThermometer"],
           ["label" => "Thermistor", "uri" => "http://www.opensilex.org/vocabulary/oeso#Thermistor"],
           ["label" => "Thermocouple", "uri" => "http://www.opensilex.org/vocabulary/oeso#Thermocouple"],
       ];

       $windSensors = [
           ["label" => "CupAnemometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#Tensiometer"],
           ["label" => "Propeller Anemometer", "uri" => "http://www.opensilex.org/vocabulary/oeso#PropellerAnemometer"],
           ["label" => "Ultrasonic Wind Sensor", "uri" => "http://www.opensilex.org/vocabulary/oeso#UltrasonicWindSensor"],
       ];

       $weighingScale = [
           ["label" => "Weighing Scale", "uri" => "http://www.opensilex.org/vocabulary/oeso#WeighingScale"],
       ];

    $sensingDevicesNumber = count($cameras) + count($humiditySensors) + count($capacitanceSensors) 
            + count($electricalResistanceBlocks) + count($precipitationSensors) + count($radiationSensors)
            + count($spectroradiometers) + count($temperatureSensors) + count($sensingDevices)
            + count($windSensors);
    
    $images = [
           ["label" => "Image", "uri" => "http://www.opensilex.org/vocabulary/oeso#Image"],
           ["label" => "Hemispherical Image", "uri" => "http://www.opensilex.org/vocabulary/oeso#HemisphericalImage"],
           ["label" => "Multispectral Image", "uri" => "http://www.opensilex.org/vocabulary/oeso#MultispectralImage"],
           ["label" => "NIR Image", "uri" => "http://www.opensilex.org/vocabulary/oeso#NIRImage"],
           ["label" => "RGB Image", "uri" => "http://www.opensilex.org/vocabulary/oeso#RGBImage"],
           ["label" => "TIR Image", "uri" => "http://www.opensilex.org/vocabulary/oeso#TIRImage"],
       ];
    
    
    $vectors = [
        ["label" => "Field Robot", "uri" => "http://www.opensilex.org/vocabulary/oeso#FieldRobot"],
        ["label" => "Gantry", "uri" => "http://www.opensilex.org/vocabulary/oeso#Gantry"],
        ["label" => "UAV", "uri" => "http://www.opensilex.org/vocabulary/oeso#UAV"]
    ]
    
?>

<div class="site-ontology">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <h2><?= Html::encode(Yii::t('app', 'Summary')) ?></h2>
    
    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= Html::a(Yii::t('yii', 'Scientific Objects'), ['ontology', '#' => 'scientific-objects']) ?>
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

    <h2 id="agronomical-objects"><?= Html::encode(Yii::t('app', 'Scientific Objects')) ?></h2>
    
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
