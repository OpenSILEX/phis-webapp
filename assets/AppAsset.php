<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        '\webtoucher\cookie\AssetBundle'
    ];
    
    //SILEX:info
    //Force le chargement des libraries JS au début de la page (pour JQUERY)
    // /!\ ce n'est pas une bonne solution pour les temps de chargement etc.
    //sera à rectifier (Cf. https://stackoverflow.com/questions/21600383/referenceerror-is-not-defined-yii2)
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    //\SILEX:info
    
}
