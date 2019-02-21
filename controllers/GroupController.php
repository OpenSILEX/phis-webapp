<?php

//**********************************************************************************************
//                                       GroupController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: implements the CRUD for the groups (wsgroupmodel)
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiGroupModel;
use app\models\yiiModels\GroupSearch;

/**
 * CRUD actions for YiiGroupModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiGroupModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class GroupController extends Controller {
    
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
     * Get Group informations by it's id
     * @param String $uri the searched group's uri
     * @return string|YiiGroupModel The YiiGroupModel representing the group
     *                              "token" is the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $groupModel = new YiiGroupModel(null, null);
        $requestRes = $groupModel->findByName($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $groupModel;
        } else if (isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * List all groups
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new GroupSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;
        if (isset($searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE]--;
        }
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
                'dataProvider' => $searchResult
            ]);
        }
    }
    
    /**
     * Displays a single Group model
     * @param string $id the name of the group
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
     * @param mixed $users users list
     * @return ArrayHelper of the users email => email
     */
    private function usersToMap($users) {
        if ($users !== null) {
            return \yii\helpers\ArrayHelper::map($users, 'email', 'email');            
        } else {
            return null;
        }
    }
    
    /**
     * Create a group
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $groupModel = new YiiGroupModel(null, null);
        
        //If the form is complete
        if ($groupModel->load(Yii::$app->request->post())) {
            $groupModel->isNewRecord = true;
            $dataToSend[] = $groupModel->attributesToArray();
            
            $requestRes = $groupModel->insert($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else if (isset($requestRes->{'metadata'}->{'datafiles'}[0])) { //group created
                return $this->redirect(['view', 'id' => $requestRes->{'metadata'}->{'datafiles'}[0]]);
            } else { //an error occurred
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $requestRes->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}]);
            }
        } else { 
            if ($sessionToken !== null) {
                $searchUsersModel = new \app\models\yiiModels\UserSearch();
                $users = $this->usersToMap($searchUsersModel->find($sessionToken, []));
                $this->view->params['listUsers'] = $users;
                $groupModel->isNewRecord = true;
                return $this->render('create', [
                    'model' => $groupModel,
                ]);
            } else {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
        }
    }
    
    /**
     * update a group
     * @param string $id the uri of the group to update 
     * @return mixed
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $groupModel = new YiiGroupModel(null, null);
        
        //Form has been complete
        if ($groupModel->load(Yii::$app->request->post())) {
            $groupModel->isNewRecord = true;
            $dataToSend[] = $groupModel->attributesToArray();
            $requestRes = $groupModel->update($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === "token") { //user must connect
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $groupModel->uri]);
            }
        } else {
            if ($sessionToken !== null) {
                $model = $this->findModel($id);
                $actualMember = null;
                if ($model->users != null && isset($model->users)) {
                    foreach ($model->users as $user) {
                        $actualMember[] = $user["email"];
                    }
                }
                
                $searchUsersModel = new \app\models\yiiModels\UserSearch();
                $users = $this->usersToMap($searchUsersModel->find($sessionToken, []));
                $this->view->params['listUsers'] = $users;
                $this->view->params['listActualMembers'] = $actualMember;
                $model->isNewRecord = false;
                return $this->render('update', [
                    'model' => $model,
                ]);
            } else {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
        }
    }
}
