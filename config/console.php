<?php
//******************************************************************************
//                                       view.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 Aug, 2018
// Contact: morgane.vidal@inra.fr,arnaud.charleroy, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

/**
 * Manage log and debug features
 * @update [Arnaud Charleroy] 28 August, 2018 : add debug configuration
 * to fix 404 error resources not found
 */

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    //SILEX:Info 
    // Debug bar configuration
    //\SILEX:info
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'debug' => [
            'class' => 'yii\debug\Module',
            // uncomment and adjust the following to add your IP if you are not connecting from localhost.
            'allowedIPs' => ['127.0.0.1', '::1', 'localhost'],
        ]
    ];
}

return $config;
