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
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\ExperimentSearch;
use app\models\yiiModels\YiiModelsConstants;
/**
 * Implements the controller for the Projects and according to YiiProjectModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiProjectModel
 * @update [Arnaud Charleroy] 14 September, 2018: increase list of users displayed
 * @update [Andréas Garcia] 11 March, 2019: Add event widget
 * @author Morgane Vidal <morgane.vidal@inra.fr>, Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class ProjectController extends Controller {
    
    CONST ANNOTATIONS_DATA = "projectAnnotations";
    CONST EXPERIMENTS = "experiments";
    CONST EVENTS = "events";
    
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
     * @action Displays a single Project model
     * @return mixed
     */
    public function actionView($id) {
        //0. Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        
        //1. get project's metadata
        $res = $this->findModel($id);
        
        //2. get project's documents list
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $id]);
        
        //3. get project annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $projectAnnotations = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
        
        //4. Get project experiments
        $searchExperimentModel = new ExperimentSearch();
        $searchExperimentModel->projectUri = $id;
        $projectExperiments = $searchExperimentModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [
            ExperimentSearch::PROJECT_URI => $id,
            YiiModelsConstants::PAGE => (Yii::$app->request->get(YiiModelsConstants::PAGE, 1) - 1)
        ]);
        
        //6. get events
        $searchEventModel = new EventSearch();
        $searchEventModel->concernedItemUri = $id;
        $searchEventModel->pageSize = Yii::$app->params['eventWidgetPageSize'];
        $events = $searchEventModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        
        if ($res === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documents,
                self::ANNOTATIONS_DATA => $projectAnnotations,
                self::EXPERIMENTS => $projectExperiments,
                self::EVENTS => $events
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
     * @action Create a Project
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session['access_token'];
        $projectModel = new YiiProjectModel(null, null);
        
        //If the form is filled, create project
        if ($projectModel->load(Yii::$app->request->post())) {
            $projectModel->isNewRecord = true;
            $dataToSend[] = $projectModel->attributesToArray();
            
            $requestRes = $projectModel->insert($sessionToken, $dataToSend);
            
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
        } else { //If the form is not filled, it should be generate
            //Get the already existing project for the dropdownlist
            //SILEX:conception
            // This quick fix is used to show all users available. We need 
            // to discuss another way to populate dropdown lists.
            //SILEX:conception
            $searchModel = new ProjectSearch();
            $projects = $searchModel->find($sessionToken,[]);
            
            $userModel = new \app\models\yiiModels\YiiUserModel();
            $contacts = $userModel->getPersonsMailsAndName($sessionToken);
            
            if (is_string($projects)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $projects]);
            } else if (is_array ($projects) && isset($projects["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
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
            
            $userModel = new \app\models\yiiModels\YiiUserModel();
            $contacts = $userModel->getPersonsMailsAndName($sessionToken);
            
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
