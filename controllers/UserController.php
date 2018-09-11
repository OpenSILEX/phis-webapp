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

use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\UserSearch;
use app\models\yiiModels\GroupSearch;

/**
 * Implements the CRUD actions for YiiUserModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiUserModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @update [Arnaud Charleroy] 23 August, 2018 : add annotations list linked to instance view and update coding style
 */
class UserController extends Controller {
    
    /**
     * define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'delete' => ['POST'],
              ]
          ]  
        ];
    }
    
    /**
     * Search a user by email
     * @param String $email searched user's email
     * @return string|YiiUserModel "token" if user must login
     *                             YiiUserModel : the searched user
     */
    public function findModel($email) {
        $sessionToken = Yii::$app->session['access_token'];
        $userModel = new YiiUserModel(null, null);
        $requestRes = $userModel->findByEmail($sessionToken, $email);
        
        if ($requestRes === true) {
            return $userModel;
        } else if (isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * List all Users
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserSearch();
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);
       
        if (is_string($searchResult)) {
            return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $searchResult]);
        } else if (is_array($searchResult) && isset($searchResult["token"])) { //user must log in
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
                'dataProvider' => $searchResult
            ]);
        }
    }
    
    /**
     * Displays a single User model
     * @return mixed
     */
    public function actionView($id) {
        $res = $this->findModel($id);
        if ($res === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                'model' => $res,
            ]);
        }
    }
    
    /**
     * 
     * @param mixed $groups groups list
     * @return ArrayHelper of the groups uri => name
     */
    private function groupsToMap($groups) {
        if ($groups !== null) {
            return \yii\helpers\ArrayHelper::map($groups, 'uri', 'name');
        } else {
            return null;
        }
    }
    
    /**
     * Create a User
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $userModel = new YiiUserModel(null, null);
        
        //Form has been complete
        if ($userModel->load(Yii::$app->request->post())) {
            $userModel->isNewRecord = true;
            $userModel->groups = Yii::$app->request->post('YiiUserModel')['groups'];
            $userModel->password = md5($userModel->password);

            $dataToSend[] = $userModel->attributesToArray();
            
            $requestRes = $userModel->insert($sessionToken, $dataToSend);    
            
            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $userModel->email]);
            }
        } else {
            $searchGroupModel = new GroupSearch();
            $groups = $searchGroupModel->find($sessionToken,[]);
            
            if (is_string($groups)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $groups]);
            } else if (is_array ($groups) && isset($groups["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $groups = $this->groupsToMap($groups);
                $this->view->params['listGroups'] = $groups;
                $userModel->isNewRecord = true;

                return $this->render('create', [
                    'model' => $userModel,
                ]);
            }
        }
    }
    
    /**
     * update a user
     * @return mixed
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $userModel = new YiiUserModel(null, null);
        
        //Form has been complete
        if ($userModel->load(Yii::$app->request->post())) {
            $userModel->isNewRecord = true;
            if (isset(Yii::$app->request->post('YiiUserModel')['groups'])) {
                $userModel->groups = Yii::$app->request->post('YiiUserModel')['groups'];
            }
            if (isset($userModel->password) && $userModel->password !== "") {
                $userModel->password = md5($userModel->password);
            }

            $dataToSend[] = $userModel->attributesToArray();
            
            $requestRes = $userModel->update($sessionToken, $dataToSend);    
            
            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $userModel->email]);
            }
        } else {
            $model = $this->findModel($id);
            $model->password = "";
            
            $actualGroups = null;
            if ($model->groups !== null) {
                foreach ($model->groups as $group) {
                    $actualGroups[] = $group["uri"];
                }
            }
            
            $searchGroupModel = new GroupSearch();
            $groups = $searchGroupModel->find($sessionToken,[]);
            
            if (is_string($groups)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $groups]);
            } else if (is_array ($groups) && isset($groups["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $groups = $this->groupsToMap($groups);
                $this->view->params['listGroups'] = $groups;
                $this->view->params['listActualGroups'] = $actualGroups;
                $model->isNewRecord = false;

                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
    
    /**
     * Return user instances list.
     * Format [user uri => user name]
     * e.g. ["http://www.phenome-fppn.fr/platform/agent/marie-emilie-jolie_dupont-prad" => "Marie-émilie jolie  Dupont-Prad"]
     * @return array users instances list
     */
    public static function getUsersUriNameInstances() {
        $searchUserModel = new UserSearch();
        $requestRes = $searchUserModel->find(Yii::$app->session[\app\models\wsModels\WSConstants::ACCESS_TOKEN], ["pageSize" => 10000]);
        
        //SILEX:info
        // if WS return a response
        //\SILEX:info
        if ($requestRes !== null) { 
            if (!is_string($requestRes)) { 
                foreach ($requestRes as $user) {
                    $userInstances[$user->uri] = $user->firstName . " " . $user->familyName;
                }
                return $userInstances;
            } else { 
                //SILEX:info
                // token invalid case
                //\SILEX:info
                if ($requestRes === \app\models\wsModels\WSConstants::TOKEN) { //L'utilisateur doit se connecter
                    return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                }
            }
        } 
        return $requestRes;
    }
}
