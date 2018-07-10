<?php

//******************************************************************************
//                                       AnnotationController.php
//
// Author(s): Arnaud Charleroy <arnaud.charleroy@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 10 july 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  10 july 2018
// Subject: implements the CRUD actions for Annotation Model
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\yiiModels\YiiAnnotationModel;
use app\models\wsModels\WSUriModel;
use app\models\yiiModels\YiiUserModel;

require_once '../config/config.php';

/**
 * Controller for Annotation model.
 * Implements the CRUD actions for Annotation Model
 * 
 * @author Guilhem HEINRICH <guilhem.heinrich@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @copyright 2011-2018 INRA
 * @license   https://github.com/OpenSILEX/phis2-ws/blob/master/LICENSE GNU Affero General Public License v3.0
 * @link      https://www.w3.org/TR/annotation-vocab
 */
class AnnotationController extends Controller {

    /**
     * Php motivation instanaces session values name
     * Use e.g. Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES];
     */
    const MOTIVATION_INSTANCES = "motivation_instances";

    /**
     * Creates a new Annotation model.
     * If creation is successful, the browser will be redirected to the ObjectRDF 'view' page.
     * 
     * @param mixed $attributes An array of value to populate the model
     * 
     * @return mixed
     */
    public function actionCreate($attributes = null) {
        $session = Yii::$app->session;
        $sessionToken = $session['access_token'];
        $annotationModel = new YiiAnnotationModel(null, null);
        // load parameters
        $annotationModel->load(Yii::$app->request->get(), '');
        // load motivation instances list
        $motivationIndividuals = $this->getMotivationInstances();
        //If user as validate form
        if ($annotationModel->load(Yii::$app->request->post())) {
            // Set model creator 
            $userModel = new YiiUserModel();
            $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
            $annotationModel->creator = $userModel->uri;
            $annotationModel->isNewRecord = true;
            $dataToSend[] = $annotationModel->attributesToArray();
            // send data
            $requestRes = $annotationModel->insert($sessionToken, $dataToSend);
            if (is_string($requestRes) && $requestRes === \app\models\wsModels\WSConstants::TOKEN) { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $annotationModel->uri = $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES}[0];
                return $this->redirect(['view', 'id' => $annotationModel->uri]);
            }
        } else {
            return $this->render(
                            'create', [
                        'model' => $annotationModel,
                        'motivationIndividuals' => $motivationIndividuals,
                            ]
            );
        }
    }

    /**
     * list all annotations
     * @return mixed
     */
    public function actionIndex() {
        // initialize annotation search model
        $searchModel = new \app\models\yiiModels\AnnotationSearch();
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);
        // load motivation instances list
        $motivationIndividuals = $this->getMotivationInstances();
        if (is_string($searchResult)) {
            return $this->render('/site/error', [
                        'name' => 'Internal error',
                        'message' => $searchResult]);
        } else if (is_array($searchResult) && isset($searchResult["token"])) { //user must log in
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        'motivationIndividuals' => $motivationIndividuals
            ]);
        }
    }

    /**
     * Return motivation instances list.
     * Format [uri => instance name]
     * e.g. ["http://www.w3.org/ns/oa#assessing" => "assessing"]
     * @return array motivation instances list
     */
    public function getMotivationInstances() {
        if (isset(Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES]) && !empty(Yii::$app->session['motivation_instances'])) {
            return Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES];
        }

        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getInstances(Yii::$app->session['access_token'], Yii::$app->params['Motivation'], ["pageSize" => 100]);

        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return \app\models\wsModels\WSConstants::TOKEN;
            } else {
                foreach ($requestRes as $motivation) {
                    $motivationIndividuals[$motivation->uri] = explode("#", $motivation->uri)[1];
                }
                Yii::$app->session['motivation_instances'] = $motivationIndividuals;
                return $motivationIndividuals;
            }
        } else {
            if ($requestRes === \app\models\wsModels\WSConstants::TOKEN) { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
            return $requestRes;
        }
    }

    /**
     * @action Displays a single annotation model
     * @return mixed
     */
    public function actionView($id) {
        $res = $this->findModel($id);

        if ($res === \app\models\wsModels\WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                        'model' => $res,
            ]);
        }
    }

    /**
     * Get an annotation informations by it's uri
     * @param String $uri searched annotation's uri
     * @return string|YiiAnnotationModel The YiiAnnotationModel representing the group
     *                                "token" is the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $annotationModel = new YiiAnnotationModel(null, null);
        $requestRes = $annotationModel->findByURI($sessionToken, $uri);
        if ($requestRes === true) {
            return $annotationModel;
        } else if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
            return \app\models\wsModels\WSConstants::TOKEN;
        } else {
            throw new NotFoundHttpException('The requested page does not exist');
        }
    }

}
