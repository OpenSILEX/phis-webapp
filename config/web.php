<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'sourceLanguage' => 'en',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'enableCookieValidation' => true,
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'DR3Noyx3yk79iUZ_I8_YXXRAu128tbyl',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        //Translation
        'i18n' => [
            'translations' => [
                //liste d'expressions régulières pour déterminer où chercher le fichier
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //chemin des fichiers
                    'basePath' => '@app/translations',
                    'fileMap' => [
                        //affinage de l'expression régulière avec fichier correspondant
                        'app' => 'app.php',
                        'app/messages' => 'messages.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'yii*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/translations',
                    'fileMap' => [
                        'yii' => 'yii.php',
                    ],
                ],
            ],
        ],
    /*
      'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'rules' => [
      ],
      ],
     */
    ],
    'params' => $params,
    'on beforeRequest' => function() {
        $preferredLanguage = isset(Yii::$app->request->cookies['language']) ? (string) Yii::$app->request->cookies['language'] : null;
        if ($preferredLanguage === null) {
            $preferredLanguage = 'en';
        }
        Yii::$app->language = $preferredLanguage;
    }
];

//YII DEBUG BAR POUR PROD
//if (YII_ENV_DEV) {
//    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = [
//        'class' => 'yii\debug\Module',
//        // uncomment the following to add your IP if you are not connecting from localhost.
//        'allowedIPs' => ['*'],
//    ];
//
//    $config['bootstrap'][] = 'gii';
//    $config['modules']['gii'] = [
//        'class' => 'yii\gii\Module',
//        // uncomment the following to add your IP if you are not connecting from localhost.
//        //'allowedIPs' => ['127.0.0.1', '::1'],
//    ];
//}

return $config;
