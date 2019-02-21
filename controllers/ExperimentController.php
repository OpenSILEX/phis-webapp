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
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;

/**
 * CRUD actions for YiiExperimentModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiExperimentModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class ExperimentController extends Controller {
    CONST ANNOTATIONS_DATA = "experimentAnnotations";
    
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
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get experiment's agronomical objects
        $searchAgronomicalObject = new \app\models\yiiModels\ScientificObjectSearch();
        $searchAgronomicalObject->experiment = $id;
        $searchParams = Yii::$app->request->queryParams;
        $agronomicalObjects = $searchAgronomicalObject->search(Yii::$app->session['access_token'], $searchParams);
         
        //4. get project annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $experimentAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
        
        //5. get all variables
        $variableModel = new \app\models\yiiModels\YiiVariableModel();
        $variables = $variableModel->getInstancesDefinitionsUrisAndLabel(Yii::$app->session['access_token']);
        
        //6. Get all sensors
        $sensorModel = new \app\models\yiiModels\YiiSensorModel();
        $sensors = $sensorModel->getAllSensorsUrisAndLabels(Yii::$app->session['access_token']);
        
        if ($res === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            $canUpdate = $this->isUserInExperimentOwnerGroup($res);
            $this->view->params['canUpdate'] = $canUpdate;
            
            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                'dataAgronomicalObjectsProvider' => $agronomicalObjects,
                self::ANNOTATIONS_DATA => $experimentAnnotations,
                'variables' => $variables,
                'sensors' => $sensors
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
     * @action Create an Experiment
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $experimentModel = new YiiExperimentModel(null, null);
        
        //If the form is complete, try to save data
        if ($experimentModel->load(Yii::$app->request->post())) {
            $experimentModel->isNewRecord = true;
            
            $dataToSend[] = $experimentModel->attributesToArray();
            
            $requestRes = $experimentModel->insert($sessionToken, $dataToSend);
            if (is_string($requestRes) && $requestRes === "token") { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                if (isset($requestRes->{'metadata'}->{'datafiles'}[0])) { //project created
                    return $this->redirect(['view', 'id' => $requestRes->{'metadata'}->{'datafiles'}[0]]);
                } else { //an error occurred
                    return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $requestRes->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}]);
                }
            }
        } else { 
            $searchProjectModel = new ProjectSearch();
            $projects = $searchProjectModel->find($sessionToken,[]);
            
            $userModel = new \app\models\yiiModels\YiiUserModel();
            $contacts = $userModel->getPersonsMailsAndName($sessionToken);
            
            $groups = null;
            
            if (Yii::$app->session['isAdmin']) {
                $searchGroupModel = new GroupSearch();
                $groups = $searchGroupModel->find($sessionToken,[]);
            } else {
                $groups = Yii::$app->session['groups'];
            }

            if (is_string($projects) || is_string($groups)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => is_string($projects) ? $projects : $groups]);
            } else if (is_array($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $groups = $this->groupsToMap($groups);
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
            
            $userModel = new \app\models\yiiModels\YiiUserModel();
            $contacts = $userModel->getPersonsMailsAndName($sessionToken);

            if (is_string($projects) || is_string($groups)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => is_string($projects) ? $projects : $groups]);
            } else if (is_array($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $groups = $this->groupsToMap($groups);
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
    
    /**
     * Ajax action to update the list of variables measured by an experiment
     * @return the webservice result with sucess or error
     */
    public function actionUpdateVariables() {
        $post = Yii::$app->request->post();
        $sessionToken = Yii::$app->session['access_token'];        
        $experimentUri = $post["uri"];
        if (isset($post["items"])) {
            $variablesUri = $post["items"];
        } else {
            $variablesUri = [];
        }
        $experimentModel = new YiiExperimentModel();
        
        $res = $experimentModel->updateVariables($sessionToken, $experimentUri, $variablesUri);
        
        return json_encode($res, JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Ajax action to update the list of sensors which participates in an experiment
     * @return the webservice result with sucess or error
     */
    public function actionUpdateSensors() {
        $post = Yii::$app->request->post();
        $sessionToken = Yii::$app->session['access_token'];        
        $experimentUri = $post["uri"];
        if (isset($post["items"])) {
            $sensorsUris = $post["items"];
        } else {
            $sensorsUris = [];
        }
        $experimentModel = new YiiExperimentModel();
        
        $res = $experimentModel->updateSensors($sessionToken, $experimentUri, $sensorsUris);
        
        return json_encode($res, JSON_UNESCAPED_SLASHES);
    }
}
