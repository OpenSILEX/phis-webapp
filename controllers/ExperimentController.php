<?php

//**********************************************************************************************
//                                       ExperimentController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 31 2017 : passage de Trial à Experiment
// Subject: implements the CRUD actions for Ws Experiment model
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiExperimentModel;
use app\models\yiiModels\ExperimentSearch;
use app\models\yiiModels\ProjectSearch;
use app\models\yiiModels\GroupSearch;
use app\models\yiiModels\UserSearch;
use app\models\yiiModels\DocumentSearch;

/**
 * CRUD actions for YiiExperimentModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiExperimentModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class ExperimentController extends Controller {
    
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
                ],
            ],
        ];
    }
    
    /**
     * Search an experiment by uri.
     * @param String $uri searched experiment's uri
     * @return mixed YiiExperimentModel : the searched experiment
     *               "token" if the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $experimentModel = new YiiExperimentModel(null, null);
        $requestRes = $experimentModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $experimentModel;
        } else if(isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * List all Experiments
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ExperimentSearch();
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);
       
        if (is_string($searchResult)) {
            return $this->render('/site/error', [
                    'name' => 'Internal error',
                    'message' => $searchResult]);
        } else if (is_array($searchResult) && isset($searchResult["token"])) { //L'utilisateur doit se connecter
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('index', [
               'searchModel' => $searchModel,
                'dataProvider' => $searchResult
            ]);
        }
    }
    
    /**
     * 
     * @param YiiExperimentModel $experimentModel
     * @return boolean true if user is in an owner group of the experiment
     */
    private function isUserInExperimentOwnerGroup($experimentModel) {
        if (isset($experimentModel->groups) && $experimentModel->groups !== null
            && isset(Yii::$app->session['groups']) && Yii::$app->session['groups'] !== null) {
            foreach ($experimentModel->groups as $experimentGroup) {
                foreach (Yii::$app->session['groups'] as $userGroup) {
                    if ($experimentGroup["uri"] === $userGroup["uri"] 
                            && $userGroup["level"] === "Owner") {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    /**
     * @action Displays a single Experiment model
     * @return mixed
     */
    public function actionView($id) {  
        //1. Get the experiment's informations
        $res = $this->findModel($id);
        
        //2. Get experiment's linked documents 
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItem = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get experiment's agronomical objects
        $searchAgronomicalObject = new \app\models\yiiModels\AgronomicalObjectSearch();
        $searchAgronomicalObject->experiment = $id;
        $agronomicalObjects = $searchAgronomicalObject->search(Yii::$app->session['access_token'], ["experiment" => $id]);
        
        if ($res === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            $canUpdate = $this->isUserInExperimentOwnerGroup($res);
            $this->view->params['canUpdate'] = $canUpdate;
            
            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                'dataAgronomicalObjectsProvider' => $agronomicalObjects
            ]);
        }
    }
    
    /**
     * 
     * @param mixed $projects projects list
     * @return ArrayHelper of the projects uri => name
     */
    private function projectsToMap($projects) {
        if ($projects !== null) {
            return \yii\helpers\ArrayHelper::map($projects, 'uri', 'name');
        } else {
            return null;
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
     * 
     * @param mixed $contacts persons list
     * @return ArrayHelper list of the persons 'email' => 'email'
     */
    private function contactsToMap($contacts) {
        if ($contacts !== null) {
            return \yii\helpers\ArrayHelper::map($contacts, 'email', 'email');
        } else {
            return null;
        }
    }
    
    /**
     * @action Create an Experiment
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $experimentModel = new YiiExperimentModel(null, null);
        
        //If the form is complete, try to save data
        if ($experimentModel->load(Yii::$app->request->post())) {
            $experimentModel->isNewRecord = true;
            
            //SILEX:todo
            //the experiment uri is generated here. It needs to be generated in
            //the web service.
            //\SILEX:todo
            
            //1. search how many experiments has the same campaign
            $searchModel = new ExperimentSearch();
            $searchModel->campaign = $experimentModel->campaign;
            $experiments = $searchModel->search($sessionToken, []);
            $numberExperiment = $experiments->getCount() + 1;
            
            //2. update uri
            $experimentModel->uri = str_replace("?", $numberExperiment, $experimentModel->uri);
            $dataToSend[] = $experimentModel->attributesToArray();
            
            $requestRes = $experimentModel->insert($sessionToken, $dataToSend);
            if (is_string($requestRes) && $requestRes === "token") { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $experimentModel->uri]);
            }
        } else { 
            $searchProjectModel = new ProjectSearch();
            $projects = $searchProjectModel->find($sessionToken,[]);
            
            $searchUserModel = new UserSearch();
            $contacts = $searchUserModel->find($sessionToken, []);
            
            $groups = null;
            
            if (Yii::$app->session['isAdmin']) {
                $searchGroupModel = new GroupSearch();
                $groups = $searchGroupModel->find($sessionToken,[]);
            } else {
                $groups = Yii::$app->session['groups'];
            }

            if (is_string($projects) || is_string($groups)) {
                return $this->render('/site/error', [
                    'name' => 'Internal error',
                    'message' => is_string($projects) ? $projects : $groups]);
            } else if (is_array($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $groups = $this->groupsToMap($groups);
                $contacts = $this->contactsToMap($contacts);
                $this->view->params['listProjects'] = $projects;
                $this->view->params['listGroups'] = $groups;
                $this->view->params['listContacts'] = $contacts;
                $experimentModel->isNewRecord = true;

                return $this->render('create', [
                    'model' => $experimentModel,
                ]);
            }
        }
    }
    
    /**
     * show update form and update data
     * @param string $id experiment uri
     * @return mixed
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $experimentModel = new YiiExperimentModel(null, null);
        
        //If the form is complete, try to update data
        if ($experimentModel->load(Yii::$app->request->post())) {
            $experimentModel->isNewRecord = true;
            
            $dataToSend[] = $experimentModel->attributesToArray();
            
            $requestRes = $experimentModel->update($sessionToken, $dataToSend);

            if (is_string($requestRes) && $requestRes === "token") { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $experimentModel->uri]);
            }
        } else { 
            $model = $this->findModel($id);
            
            $actualProjects = null;
            $actualGroups = null;
            $actualScientificSupervisors = null;
            $actualTechnicalSupervisors = null;
            
            if ($model->projects != null){
                foreach ($model->projects as $project) {
                    $actualProjects[] = $project["uri"];
                }
            }
            
            if ($model->groups != null) {
                foreach ($model->groups as $group) {
                    $actualGroups[] = $group["uri"];
                }
            }
            
            if ($model->scientificSupervisorContacts != null) {
                foreach ($model->scientificSupervisorContacts as $scientificSupervisor) {
                    $actualScientificSupervisors[] = $scientificSupervisor["email"];
                }
            }
            
            if ($model->technicalSupervisorContacts != null) {
                foreach ($model->technicalSupervisorContacts as $technicalSupervisor) {
                    $actualTechnicalSupervisors[] = $technicalSupervisor["email"];
                }
            }
            
            $searchProjectModel = new ProjectSearch();
            $projects = $searchProjectModel->find($sessionToken,[]);
            
            $searchGroupModel = new GroupSearch();
            $groups = $searchGroupModel->find($sessionToken,[]);
            
            $searchUserModel = new UserSearch();
            $contacts = $searchUserModel->find($sessionToken, []);

            if (is_string($projects) || is_string($groups)) {
                return $this->render('/site/error', [
                    'name' => 'Internal error',
                    'message' => is_string($projects) ? $projects : $groups]);
            } else if (is_array($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $groups = $this->groupsToMap($groups);
                $contacts = $this->contactsToMap($contacts);
                $this->view->params['listProjects'] = $projects;
                $this->view->params['listActualProjects'] = $actualProjects; 
                $this->view->params['listGroups'] = $groups;
                $this->view->params['listActualGroups'] = $actualGroups; 
                $this->view->params['listContacts'] = $contacts;
                $this->view->params['listActualScientificSupervisors'] = $actualScientificSupervisors;
                $this->view->params['listActualTechnicalSupervisors'] = $actualTechnicalSupervisors;
                $model->isNewRecord = false;

                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
}
