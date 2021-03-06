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
$serie = [
    "name" => $sensorGraphData["graphName"],
    "data" => []
];

if (is_array($sensorGraphData['data'])) {
    foreach ($sensorGraphData['data'] as $data) {
        $serie['data'][] = [(strtotime($data->date))*1000, $data->value ];
    }

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
            'series' => [$serie],
        ]
    ]);
}
?>
