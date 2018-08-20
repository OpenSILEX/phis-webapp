<?php
//**********************************************************************************************
//                                       main.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 3 2017 (add dataset creation link)
// Subject: the main view page
//***********************************************************************************************

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

require_once(__DIR__ . '/../../config/config.php');
require_once(__DIR__ . '/../../config/web_services.php');

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    
    
    NavBar::begin([
        'brandLabel' => 'PHIS <i> ' . Yii::$app->params['platform'] . '</i>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems;
    //Cas d'un utilisateur non connecté (invité)
    if (Yii::$app->session['isGuest'] || Yii::$app->session['isGuest'] === null) {
        $menuItems = [['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']]];
        
        
    } else if (Yii::$app->session['isAdmin']) { //Cas d'un admin
        $menuItems[] = ['label' => Yii::t('app', 'Experimental Organization'),
                        'items' => [
                            [
                                'label' => '<span class="glyphicon glyphicon-home" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Infrastructure} other{Infrastructures}}', ['n' => 2]), 
                                'url' => ['/infrastructure/view', 'uri' => "http://www.phenome-fppn.fr/diaphen"],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 
                                'url' => ['/project/index'],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-grain" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]),
                                'url' => ['/experiment/index']
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-leaf" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Agronomical Object} other{Agronomical Objects}}', ['n' => 2]),
                                'url' => ['/agronomical-object/index']
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> ' . Yii::t('app', 'Variables'), 
                                'url' => ['/variable/index']
                            ]
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Dataset'), 'url' => ['/dataset/create']];
        $menuItems[] = ['label' => Yii::t('app', 'Installation'),
                        'items' => [
                            [
                                'label' => '<span class="glyphicon glyphicon-camera" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 
                                'url' => ['/sensor/index']
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 
                                'url' => ['/vector/index']
                            ]
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Tools'),
                        'items' => [
                                [
                                    'label' => '<span class="glyphicon glyphicon-th" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]), 
                                    'url' => ['/group/index']
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 
                                    'url' => ['/user/index']
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-book" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 
                                    'url' => ['/document/index']
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-fire" aria-hidden="true"></span> ' . Yii::t('app', 'Web API'), 
                                    'url' => WS_PHIS_PATH_DOC
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-link" aria-hidden="true"></span> ' . Yii::t('app', 'Documentation'), 
                                    'url' => "http://147.100.175.121/phis-docs-community/"
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> ' . Yii::t('app', 'Vocabulary'), 
                                    'url' => ['/site/ontology']
                                ],
                            ]
                        ];
        
        $menuItems[] = [
                            'label' => '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ' . Yii::t('app', 'Logout'). ' ('. Yii::$app->session['email']. ')', 
                            'url' => ['/site/disconnect']
                        ];
    } else { // Cas d'un utilisateur simple connecté
        $menuItems[] = ['label' => Yii::t('app', 'Experimental Organization'),
                        'items' => [
                            [
                                'label' => '<span class="glyphicon glyphicon-home" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Infrastructure} other{Infrastructures}}', ['n' => 2]), 
                                'url' => ['/infrastructure/view', 'uri' => "http://www.phenome-fppn.fr/diaphen"],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 
                                'url' => ['/project/index'],
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-grain" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]),
                                'url' => ['/experiment/index']
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-leaf" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Agronomical Object} other{Agronomical Objects}}', ['n' => 2]),
                                'url' => ['/agronomical-object/index']
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> ' . Yii::t('app', 'Variables'), 
                                'url' => ['/variable/index']
                            ]
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Installation'),
                        'items' => [
                            [
                                'label' => '<span class="glyphicon glyphicon-camera" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 
                                'url' => ['/sensor/index']
                            ],
                            [
                                'label' => '<span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 
                                'url' => ['/vector/index']
                            ]
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Tools'),
                        'items' => [
                                [
                                    'label' => '<span class="glyphicon glyphicon-th" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]), 
                                    'url' => ['/group/index']
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 
                                    'url' => ['/user/index']
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-book" aria-hidden="true"></span> ' . Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 
                                    'url' => ['/document/index']
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-fire" aria-hidden="true"></span> ' . Yii::t('app', 'Web API'), 
                                    'url' => WS_PHIS_PATH_DOC
                                ],
                                [
                                    'label' => '<span class="glyphicon glyphicon-link" aria-hidden="true"></span> ' . Yii::t('app', 'Documentation'), 
                                    'url' => "http://147.100.175.121/phis-docs-community/"
                                ],
                            ]   
                        ];
        
        $menuItems[] = [
                            'label' => '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ' . Yii::t('app', 'Logout'). ' ('. Yii::$app->session['email']. ')', 
                            'url' => ['/site/disconnect']
                        ];
    }
    
    
    
//    $menuItems[] = [
//        'label' => strtoupper(Yii::$app->language),
//        'items' => [
//            
//            Yii::$app->language === "en" ? ['label' => 'FR', 'url' => ['site/language', 'language' => 'fr']] : ['label' => 'EN', 'url' => ['site/language', 'language' => 'en']]
//            
////            ['label' => 'EN', 'url' => ['site/language', 'language' => 'en']],
////            '<li class="divider"></li>',
////            ['label' => 'FR', 'url' => ['site/language', 'language' => 'fr']],
//        ]
//        
//        ];
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => $menuItems 
    ]);
    NavBar::end();
    ?>

    <div class="container">
        
        <div class="pull-right">
            
            <?php
                $urlFlag = \config::path()['basePath'] . '/images/icons/flags/';
                $urlLangage = \config::path()['baseIndexPath'] . '?r=site%2Flanguage&flag=';
            ?> 
            <?= Html::a('<img title="english" alt="EN", src="' . $urlFlag . 'drapeau-rond-en.png">', ['site/language', 'language' => 'en'], []) ?>
            <?= Html::a('<img title="french" alt="FR", src="' . $urlFlag . 'drapeau-rond-fr.png">', ['site/language', 'language' => 'fr'], []) ?>    
        </div>
        <div class="clearfix"></div>
        
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>


<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; INRA MISTEA 2014-2018 (SILEX-PHIS v.2.4 - 01 August 2018)</p>
    </div> 
</footer> 


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
