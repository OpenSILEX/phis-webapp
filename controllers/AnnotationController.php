<?php
//******************************************************************************
//                          AnnotationController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 13 Jul, 2018
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\yiiModels\YiiAnnotationModel;
use app\models\wsModels\WSUriModel;
use app\models\yiiModels\YiiUserModel;
use app\components\helpers\Vocabulary;
use app\controllers\UserController;

/**
 * Controller for Annotation model.
 * Implements the CRUD actions for Annotation Model
 * @author Guilhem HEINRICH <guilhem.heinrich@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @update [Arnaud Charleroy] 23 august, 2018 : format with right coding style.
 * @link https://www.w3.org/TR/annotation-vocab
 */
class AnnotationController extends Controller {

    /**
     * Php motivation instances session values name
     * Use e.g. Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES];
     */
    const MOTIVATION_INSTANCES = "motivation_instances";

    /**
     * Creates a new Annotation model.
     * If creation is successful, the browser will be redirected to the ObjectRDF 'view' page.
     * @param mixed $attributes An array of value to populate the model
     * @return mixed
     */
    public function actionCreate($attributes = null) {
        $session = Yii::$app->session;
        $sessionToken = $session[\app\models\wsModels\WSConstants::ACCESS_TOKEN];
        $annotationModel = new YiiAnnotationModel(null, null);
        // Load parameters
        $annotationModel->load(Yii::$app->request->get(), '');
        // Load motivation instances list
        $motivationInstances = $this->getMotivationInstances();
        // If user as validate form
        if ($annotationModel->load(Yii::$app->request->post())) {
            // Set model creator 
            $userModel = new YiiUserModel();
            $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
            $annotationModel->creator = $userModel->uri;
            $annotationModel->isNewRecord = true;
            $dataToSend[] = $annotationModel->attributesToArray();
            // Send data
            $requestRes = $annotationModel->insert($sessionToken, $dataToSend);
            if (is_string($requestRes) && $requestRes === \app\models\wsModels\WSConstants::TOKEN) { // User must be connected
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $annotationModel->uri = $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES}[0];
                return $this->redirect(['view', 'id' => $annotationModel->uri]);
            }
        } else {
            return $this->render('create', 
                    [
                        'model' => $annotationModel,
                        AnnotationController::MOTIVATION_INSTANCES => $motivationInstances,
                    ]
            );
        }
    }

    /**
     * List all annotations
     * @return mixed
     */
    public function actionIndex() {
        // Initialize annotation search model
        $searchModel = new \app\models\yiiModels\AnnotationSearch();
        $searchResult = $searchModel->search(Yii::$app->session[\app\models\wsModels\WSConstants::ACCESS_TOKEN], Yii::$app->request->queryParams);
        
        // Load user instances list
        $userInstances = UserController::getUsersUriNameInstances();
       
        // Load once motivation instances list
        $motivationInstances = $this->getMotivationInstances();
        if (is_string($searchResult)) {
            return $this->render('/site/error', 
                    [
                        'name' => 'Internal error',
                        'message' => $searchResult
                    ]
                );
            //User must log in
        } else if (is_array($searchResult) && isset($searchResult["token"])) { 
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('index', 
                    [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        AnnotationController::MOTIVATION_INSTANCES => $motivationInstances,
                        'userInstances' => $userInstances
                    ]
                );
        }
    }

    /**
     * Return motivation instances list.
     * Format [uri => instance name]
     * e.g. ["http://www.w3.org/ns/oa#assessing" => "assessing"]
     * @return array motivation instances list
     */
    public function getMotivationInstances() {
        // Use session storage variable to prevent multiple triplestore calls
        if (isset(Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES]) && !empty(Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES])) {
            return Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES];
        }

        $wsUriModel = new WSUriModel();
        $requestRes = $wsUriModel->getInstances(Yii::$app->session[\app\models\wsModels\WSConstants::ACCESS_TOKEN], Yii::$app->params['Motivation'], ["pageSize" => 100]);

        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return \app\models\wsModels\WSConstants::TOKEN;
            } else {
                foreach ($requestRes as $motivation) {
                    $motivationInstances[$motivation->uri] = Vocabulary::prettyUri($motivation->uri);
                }
                Yii::$app->session[AnnotationController::MOTIVATION_INSTANCES] = $motivationInstances;
                return $motivationInstances;
            }
        } else {
            if ($requestRes === \app\models\wsModels\WSConstants::TOKEN) { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
            return $requestRes;
        }
    }

    /**
     * Displays a single annotation model.
     * @return mixed
     */
    public function actionView($id) {
        $res = $this->findModel($id);

        if ($res === \app\models\wsModels\WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', 
                    [
                        'model' => $res,
                    ]
                );
        }
    }

    /**
     * Get an annotation informations by it's uri.
     * @param String $uri searched annotation's uri
     * @return string|YiiAnnotationModel The YiiAnnotationModel representing the group
     *                                "token" is the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session[\app\models\wsModels\WSConstants::ACCESS_TOKEN];
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
