<?php
//******************************************************************************
//                          UserController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: Jun, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Add iframe for profile management
 * @see yii\web\Controller
 * @author Vicnent Migot
 */
class ProfileController extends Controller {
  
    /**
     * List all profiles
     * @return mixed
     */
    public function actionIndex() {
        $url = WS_PHIS_APP_PATH . "profiles?embed=true&token=".Yii::$app->session['access_token'];
        return $this->render('iframe',[
            'url'=>$url
        ]);
    }
}
