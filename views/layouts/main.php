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
        $menuItems[] = ['label' => Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 'url' => ['/project/index']];
        $menuItems[] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['/experiment/index']];
        $menuItems[] = ['label' => Yii::t('app', '{n, plural, =1{Agronomical Object} other{Agronomical Objects}}', ['n' => 2]), 'url' => ['/agronomical-object/index']];
        $menuItems[] = ['label' => Yii::t('app', 'Dataset'), 'url' => ['/dataset/create']];
        $menuItems[] = ['label' => Yii::t('app', 'Variables'), 'url' => ['/variable/index']];
        $menuItems[] = ['label' => Yii::t('app', 'Tools'),
                        'items' => [
                            ['label' => Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]), 'url' => ['/group/index']],
                            ['label' => Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 'url' => ['/user/index']],
                            ['label' => Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 'url' => ['/document/index']]
                        ]
            
        ];
        $menuItems[] = ['label' => Yii::t('app', 'Logout'). ' ('. Yii::$app->session['email']. ')', 'url' => ['/site/disconnect']];
    } else { // Cas d'un utilisateur simple connecté
        $menuItems[] = ['label' => Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 'url' => ['/project/index']];
        $menuItems[] = ['label' => Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]), 'url' => ['/experiment/index']];
        $menuItems[] = ['label' => Yii::t('app', 'Tools'),
                        'items' => [
                            ['label' => Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 'url' => ['/user/index']],
                            ['label' => Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 'url' => ['/document/index']]
                        ]
        ];
        $menuItems[] = ['label' => Yii::t('app', 'Logout'). ' ('. Yii::$app->session['email']. ')', 'url' => ['/site/disconnect']];
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
        <p class="pull-left">&copy; INRA MISTEA 2014-2018 (SILEX-PHIS v.2.2 - 18 January 2018)</p>
    </div> 
</footer> 


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
