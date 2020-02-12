<?php

//******************************************************************************
//                                       _view_sensor_graph.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 13th November 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use miloschuman\highcharts\Highcharts;
?>
<?php

// Create data serie for HighChart
/**
 * @example
 * $serie = [
 *      "name" => "TRAIT_METHOD_UNIT",
 *      "data" => [
 *          [1497516660000, 1.25],
 *          [1497516720000, 1.33]
 *      ]
 * ]
 */

$series = [];

foreach( $sensorGraphData["provenances"] as $provenance) {
    $series[$provenance->uri]= [
            "name" => $provenance->label,
            "data" => []
        ];
}

//Create an array of data to store serie data by uri
//array(1) { ["http://www.opensilex.org/sunagri/id/provenance/1572430583192"]=> array(2) { 
//["name"]=> string(25) "new provs agent + sensor2" ["data"]=> array(0) { } } }
if (is_array($sensorGraphData['data'])) {
    foreach ($sensorGraphData['data'] as $data) {
        $series[$data->provenanceUri]['data'][] = [(strtotime($data->date))*1000, $data->value ];
    }
    //format data  - remove uri key
    $series = array_values($series);
    
    // Display Hightchart widget
    echo Highcharts::widget([
        // Create a unique ID for each graph based on variable URI
        'id' => base64_encode($sensorGraphData["variableUri"]),
        'options' => [
            'time' => ['timezoneOffset' => -2 * 60],
            'title' => ['text' => $sensorGraphData["graphName"] . ' - ' . $sensorGraphData["variableUri"]],
            'xAxis' => [
                'type' => 'datetime',
                'title' => 'Date',
            ],
            'yAxis' => [
                'title' => "Value",
                'labels' => [
                    'format' => '{value:.2f}'
                ]
            ],
            'tooltip' => [
               'xDateFormat' => '%Y-%m-%d %H:%M'
             ],
            'series' => $series,
        ]
    ]);
}
?>
