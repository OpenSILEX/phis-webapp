<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\yiiModels\YiiAnnotationModel;
use app\models\wsModels\WSUriModel;
use app\models\yiiModels\YiiUserModel;

require_once '../config/config.php';

/**
 * Controller for Annotation 
 * 
 * @author Guilhem HEINRICH <guilhem.heinrich@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @copyright 2011-2018 INRA
 * @license   https://github.com/OpenSILEX/phis2-ws/blob/master/LICENSE GNU Affero General Public License v3.0
 * @link      https://www.w3.org/TR/annotation-vocab
 */
class AnnotationController extends Controller {

    /**
     * Creates a new Annotation model.
     * If creation is successful, the browser will be redirected to the ObjectRDF 'view' page.
     * 
     * @param mixed $attributes An array of value to populate the model
     * 
     * @return mixed
     */
    public function actionCreate(array $attributes = null) {
        $session = Yii::$app->session;
        $sessionToken = $session['access_token'];
        $annotationModel = new YiiAnnotationModel(null, null);

        $annotationModel->load(Yii::$app->request->get(), '');

        if (!isset($session['annotationMotivationList'])) {
            $requestResUri = $this->getMotivationInstances();
            if (is_string($requestResUri) && $requestResUri === \app\models\wsModels\WSConstants::TOKEN) { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $session['annotationMotivationList'] = $requestResUri;
            }
        }
        $motivationIndividuals = $session['annotationMotivationList'];
        //Si l'utilisateur a remplis le formulaire, on tente l'insert
        if ($annotationModel->load(Yii::$app->request->post())) {
            $userModel = new YiiUserModel();
            $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
            $annotationModel->creator = $userModel->uri;
            $annotationModel->isNewRecord = true;
            $dataToSend[] = $annotationModel->attributesToArray();
           
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

    private function getMotivationInstances() {

        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getInstances(Yii::$app->session['access_token'], Yii::$app->params['Motivation'], ["pageSize" => 100]);

        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return \app\models\wsModels\WSConstants::TOKEN;
            } else {
                foreach ($requestRes as $motivation) {
                    $motivationIndividuals[$motivation->uri] = explode("#", $motivation->uri)[1];
                }
                return $motivationIndividuals;
            }
        } else {
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
