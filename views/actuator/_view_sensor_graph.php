<?php

//******************************************************************************
//                                       _view_actuator_graph.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
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
    "name" => $actuatorGraphData["graphName"],
    "data" => []
];

if (is_array($actuatorGraphData['data'])) {
    foreach ($actuatorGraphData['data'] as $data) {
        $serie['data'][] = [(strtotime($data->date))*1000, $data->value ];
    }

    // Display Hightchart widget
    echo Highcharts::widget([
        // Create a unique ID for each graph based on variable URI
        'id' => base64_encode($actuatorGraphData["variableUri"]),
        'options' => [
            'title' => ['text' => $actuatorGraphData["graphName"] . ' - ' . $actuatorGraphData["variableUri"]],
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
