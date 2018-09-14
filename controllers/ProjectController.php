<?php
//******************************************************************************
//                            ProjectController.java
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: Mar, 2017
// Contact: morgane.vidal@inra.fr,arnaud.charleroy@inra.fr, anne.tireau@inra.fr, 
//          pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiProjectModel;
use app\models\yiiModels\ProjectSearch;
use app\models\yiiModels\UserSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;

/**
 * Implements the CRUD for the projects (ws project model : YiiProjectModel)
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiProjectModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>, Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @update [Arnaud Charleroy] 14 September, 2018 : increase list of users displayed
 */
class ProjectController extends Controller {
    
    CONST ANNOTATIONS_DATA = "projectAnnotations";
    
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
     * Get a Project's informations by it's uri
     * @param String $uri searched project's uri
     * @return string|YiiProjectModel The YiiProjectModel representing the group
     *                                "token" is the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session['access_token'];
        $projectModel = new YiiProjectModel(null, null);
        $requestRes = $projectModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $projectModel;
        } else if (isset($requestRes["token"])) {
            return "token";
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * List all Projects
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ProjectSearch();

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
     * @action Displays a single Project model
     * @return mixed
     */
    public function actionView($id) {
        //1. get project's metadata
        $res = $this->findModel($id);
        
        //2. get project's documents list
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItem = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get project annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $projectAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
        
        if ($res === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                self::ANNOTATIONS_DATA => $projectAnnotations
            ]);
        }
    }
    
   /**
     * 
     * @param mixed $projects projects list
     * @return ArrayHelper list of the persons 'uri' => 'name'
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
     * @action Create a Project
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $projectModel = new YiiProjectModel(null, null);
        
        //Si l'utilisateur a remplis le formulaire, on tente l'insert
        if ($projectModel->load(Yii::$app->request->post())) {
            $projectModel->isNewRecord = true;
            $dataToSend[] = $projectModel->attributesToArray();
            
            $requestRes = $projectModel->insert($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === "token") { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $projectModel->uri]);
            }
            
        } else { //Sinon c'est qu'il faut afficher ce formulaire
            //Récupération de la liste des projets existants pour la dropdownList
            //SILEX:conception
            // This quick fix is used to show all users available in the 
            // dropdown list but we need to implements an autocompletion
            // search linked to the web service instead of load all
            // users in a list
            //SILEX:conception
            $searchModel = new ProjectSearch();
            $projects = $searchModel->find($sessionToken, []);
            
            $searchUserModel = new UserSearch();
            $contacts = $searchUserModel->find($sessionToken, ["pageSize" => 200]);
            
            if (is_string($projects)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $projects]);
            } else if (is_array ($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $contacts = $this->contactsToMap($contacts);
                $this->view->params['listProjects'] = $projects;
                $this->view->params['listContacts'] = $contacts;
                $projectModel->isNewRecord = true;

                return $this->render('create', [
                    'model' => $projectModel,
                ]);
            }
        }
    }
    
    /**
     * update a project
     * @param string $id the project's uri
     * @return mixed
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $projectModel = new YiiProjectModel(null, null);
        
        //The form is complete
        if ($projectModel->load(Yii::$app->request->post())) {
            $projectModel->isNewRecord = true;
            $dataToSend[] = $projectModel->attributesToArray();
            
            $requestRes = $projectModel->update($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === "token") { //User must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $projectModel->uri]);
            }
            
        } else {
            //Get existing projects for the dropdownlist
            $model = $this->findModel($id);
            
            $searchModel = new ProjectSearch();
            $projects = $searchModel->find($sessionToken,[]);
            
            $searchUserModel = new UserSearch();
            $contacts = $searchUserModel->find($sessionToken, []);
            
            $actualScientificContacts = null;
            $actualAdministrativeContacts = null;
            $actualProjectCoordinators = null;
            
            if ($model->scientificContacts != null) {
                foreach ($model->scientificContacts as $scientificContact) {
                    $actualScientificContacts[] = $scientificContact["email"];
                }
            }
            
            if ($model->administrativeContacts != null) {
                foreach ($model->administrativeContacts as $administrativeContact) {
                    $actualAdministrativeContacts[] = $administrativeContact["email"];
                }
            }
            
            if ($model->projectCoordinatorContacts != null) {
                foreach ($model->projectCoordinatorContacts as $projectCoordinator) {
                    $actualProjectCoordinators[] = $projectCoordinator["email"];
                }
            }

            if (is_string($projects)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $projects]);
            } else if (is_array ($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $contacts = $this->contactsToMap($contacts);
                $this->view->params['listProjects'] = $projects;
                $this->view->params['listContacts'] = $contacts;
                $this->view->params['listActualScientificContacts'] = $actualScientificContacts;
                $this->view->params['listActualAdministrativeContacts'] = $actualAdministrativeContacts;
                $this->view->params['listActualProjectCoordinators'] = $actualProjectCoordinators;
                $model->isNewRecord = false;

                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
}
