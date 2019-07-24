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
use yii\helpers\ArrayHelper;

use app\models\yiiModels\YiiProjectModel;
use app\models\yiiModels\YiiUserModel;
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
    
    CONST ANNOTATIONS_PROVIDER = "annotationsProvider";
    CONST EXPERIMENTS_PROVIDER = "experiments";
    CONST EVENTS_PROVIDER = "eventsProvider";
    
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
     * Gets a Project's information by it's URI
     * @param String $uri searched project's URI
     * @return string|YiiProjectModel The YiiProjectModel representing the group
     *                                "token" is the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $projectModel = new YiiProjectModel(null, null);
        $requestRes = $projectModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $projectModel;
        } else if (isset($requestRes[WSConstants::TOKEN])) {
            return WSConstants::TOKEN;
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * Lists all Projects
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ProjectSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;        
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[YiiModelsConstants::PAGE]--;
        }

        $searchResult = $searchModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN_INVALID) {
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
        $documentsProvider = $searchDocumentModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], ["concernedItem" => $id]);
        
        //3. get project annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $annotationsProvider = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
        
        //4. Get project experiments
        $searchExperimentModel = new ExperimentSearch();
        $searchExperimentModel->projectUri = $id;
        $experimentsProvider = $searchExperimentModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [
            ExperimentSearch::PROJECT_URI => $id,
            YiiModelsConstants::PAGE => (Yii::$app->request->get(YiiModelsConstants::PAGE, 1) - 1)
        ]);
        
        //6. get events
        $searchEventModel = new EventSearch();
        $searchEventModel->searchConcernedItemUri = $id;
        $eventSearchParameters = [];
        if (isset($searchParams[WSConstants::EVENT_WIDGET_PAGE])) {
            $eventSearchParameters[WSConstants::PAGE] = $searchParams[WSConstants::EVENT_WIDGET_PAGE] - 1;
        }
        $eventSearchParameters[WSConstants::PAGE_SIZE] = Yii::$app->params['eventWidgetPageSize'];
        $eventsProvider = $searchEventModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $eventSearchParameters);
        $eventsProvider->pagination->pageParam = WSConstants::EVENT_WIDGET_PAGE;
        
        if ($res === WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documentsProvider,
                self::ANNOTATIONS_PROVIDER => $annotationsProvider,
                self::EXPERIMENTS_PROVIDER => $experimentsProvider,
                self::EVENTS_PROVIDER => $eventsProvider
            ]);
        }
    }
    
    /**
     * @action Create a Project
     * @return mixed
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $projectModel = new YiiProjectModel();
        
        //If the form is filled, create project
        if ($projectModel->load(Yii::$app->request->post())) {
            $projectModel->isNewRecord = true;
            $dataToSend[] = $projectModel->attributesToArray();
            
            $requestRes = $projectModel->insert($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === WSConstants::TOKEN) { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                if (isset($requestRes->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0])) { //project created
                    return $this->redirect(['view', 'id' => $requestRes->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0]]);
                } else { //an error occurred
                    return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $requestRes->{WSConstants::ACCESS_TOKEN}->{WSConstants::STATUS}[0]->{WSConstants::EXCEPTION}->{WSConstants::DETAILS}]);
                }
            }
        } else { //If the form is not filled, it should be generate            
            $userModel = new YiiUserModel();
            $contacts = $userModel->getPersonsURIAndName($sessionToken);
            
            $this->view->params['listProjects'] = $projectModel->getAllProjectsUrisAndLabels($sessionToken);
            $this->view->params['listContacts'] = $contacts;
            $this->view->params['listFinancialFundings'] = $projectModel->getFinancialFundings($sessionToken);
            $projectModel->isNewRecord = true;

            return $this->render('create', [
                'model' => $projectModel,
            ]);
        }
    }
    
    /**
     * update a project
     * @param string $id the project's uri
     * @return mixed
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $projectModel = new YiiProjectModel(null, null);
        
        //The form is complete
        if ($projectModel->load(Yii::$app->request->post())) {
            $projectModel->isNewRecord = true;
            $dataToSend[] = $projectModel->attributesToArray();
            
            $requestRes = $projectModel->update($sessionToken, $dataToSend);
            if (is_string($requestRes) && $requestRes === WSConstants::TOKEN) { //User must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $projectModel->uri]);
            }
            
        } else {
            //Get existing projects for the dropdownlist
            $model = $this->findModel($id);
            
            $this->view->params['listActualScientificContacts'] = $this->getActualScientificContactsFromModel($model);
            $this->view->params['listActualCoordinators'] = $this->getActualCoordinatorsFromModel($model);
            $this->view->params['listActualAdministrativeContacts'] = $this->getActualAdministrativeContactsFromModel($model);
            $this->view->params['listActualProjects'] = $this->getActualProjects($model);
            
            $userModel = new YiiUserModel();
            $contacts = $userModel->getPersonsURIAndName($sessionToken);
            
            $this->view->params['listProjects'] = $projectModel->getAllProjectsUrisAndLabels($sessionToken);
            $this->view->params['listContacts'] = $contacts;
            $this->view->params['listFinancialFundings'] = $projectModel->getFinancialFundings($sessionToken);
            $model->isNewRecord = false;

            return $this->render('update', [
                'model' => $model,
            ]);
            
        }
    }
    
    /**
     * Get the list of uris relatedProjects of the model.
     * @param type $model
     * @return type
     */
    private function getActualProjects($model) {
        $projects = [];
        if (isset($model->relatedProjects)) {
            foreach ($model->relatedProjects as $relatedProject) {
                $projects[] = $relatedProject->uri;
            }
        }
        return $projects;
    }
    
    /**
     * Get the list of uris of the scientific contacts of the model.
     * Used to show the lists of contacts in the update form.
     * @see ProjectController::actionUpdate()
     */
    private function getActualScientificContactsFromModel($model) {
        $scientificContacts = [];
        if (isset($model->scientificContacts)) {
            foreach ($model->scientificContacts as $scientificContact) {
                $scientificContacts[] = $scientificContact->uri;
            }
        }
        return $scientificContacts;
    }
    
    /**
     * Get the list of uris of the project coordinators of the model.
     * Used to show the lists of contacts in the update form.
     * @see ProjectController::actionUpdate()
     */
    private function getActualCoordinatorsFromModel($model) {
        $coordinators = [];
        if (isset($model->projectCoordinatorContacts)) {
            foreach ($model->projectCoordinatorContacts as $projectCoordinator) {
                $coordinators[] = $projectCoordinator->uri;
            }
        }
        return $coordinators;
    }
    
    /**
     * Get the list of uris of the administrative contacts of the model.
     * Used to show the lists of contacts in the update form.
     * @see ProjectController::actionUpdate()
     */
    private function getActualAdministrativeContactsFromModel($model) {
        $administrativeContacts = [];
        if (isset($model->administrativeContacts)) {
            foreach ($model->administrativeContacts as $administrativeContact) {
                $administrativeContacts[] = $administrativeContact->uri;
            }
        }
        return $administrativeContacts;
    }
}
