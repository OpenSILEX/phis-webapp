<?php
//******************************************************************************
//                            VectorController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\YiiVectorModel;
use app\models\yiiModels\VectorSearch;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\UserSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\AnnotationSearch;
use app\models\yiiModels\YiiModelsConstants;
use app\models\wsModels\WSConstants;

/**
 * CRUD actions for vector model
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiVectorModel
 * @update [Morgane Vidal] 10 August, 2018: add link documents to vectors
 * @update [Andréas Garcia] 11 March, 2019: Add event widget
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class VectorController extends Controller {
    
    CONST ANNOTATIONS_PROVIDER = "annotationsProvider";
    CONST EVENTS_PROVIDER = "eventsProvider";
    
    /**
     * Defines the behaviors
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
     * Gets the vectors types
     * @return array list of the vectors types uris 
     * @example
     * [
     *   "UAV",
     *   "Pot"
     * ]
     */
    public function getVectorsTypes() {
        $model = new YiiVectorModel();
        
        $vectorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $vectorDevicesConcepts = $model->getVectorsTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
            if ($vectorDevicesConcepts === WSConstants::TOKEN) {
                return WSConstants::TOKEN;
            } else {
                $totalPages = $vectorDevicesConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];

                foreach ($vectorDevicesConcepts[WSConstants::DATA] as $vectorType) {
                    $vectorsTypes[] = explode("#", $vectorType->uri)[1];
                }
            }
        }
        
        return $vectorsTypes;
    }
    
    /**
     * Gets the vectors types (complete URI)
     * @return array list of the vectors types URIs 
     * @example
     * [
     *   "http://www.opensilex.org/vocabulary/oeso#UAV",
     *   "http://www.opensilex.org/vocabulary/oeso#Pot"
     * ]
     */
    public function getVectorsTypesUris() {
        $model = new YiiVectorModel();
        
        $vectorsTypes = [];
        $totalPages = 1;
        for ($i = 0; $i < $totalPages; $i++) {
            $model->page = $i;
            $vectorsConcepts = $model->getVectorsTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
            if ($vectorsConcepts === WSConstants::TOKEN) {
                return WSConstants::TOKEN;
            } else {
                $totalPages = $vectorsConcepts[WSConstants::PAGINATION][WSConstants::TOTAL_PAGES];
                foreach ($vectorsConcepts[WSConstants::DATA] as $vectorType) {
                    $vectorsTypes[] = $vectorType->uri;
                }
            }
        }
        
        return $vectorsTypes;
    }
    
    /**
     * Generates the vector creation page
     * @return mixed
     */
    public function actionCreate() {
        $model = new YiiVectorModel();
        
        $vectorsTypes = $this->getVectorsTypes();
        if ($vectorsTypes === WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        }
        
        $usersModel = new YiiUserModel();
        $users = $usersModel->getPersonsMailsAndName(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        
        
        return $this->render('create', [
            'model' => $model,
            'vectorsTypes' => json_encode($vectorsTypes, JSON_UNESCAPED_SLASHES),
            'users' => json_encode(array_keys($users))
        ]);
    }
    
    /**
     * @param string $vectorType
     * @return string the complete vector type URI corresponding to the given 
     *                vector type
     * @example http://www.opensilex.org/vocabulary/oeso#UAV
     */
    private function getVectorTypeCompleteUri($vectorType) {
        $vectorsTypes = $this->getVectorsTypesUris();
        foreach ($vectorsTypes as $sensorTypeUri) {
            if (strpos($sensorTypeUri, $vectorType)) {
                return $sensorTypeUri;
            }
        }
        return null;
    }
    
    /**
     * Creates the given vectors
     * @return string the JSON of the creation return
     */
    public function actionCreateMultipleVectors() {
        $vectors = json_decode(Yii::$app->request->post()["vectors"]);
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        
        $vectorsUris = null;
        if (count($vectors) > 0) {
            $vectorsUris = null;
            foreach ($vectors as $vector) {
                $forWebService = null;
                $vectorModel = new YiiVectorModel();
                $vectorModel->rdfType = $this->getVectorTypeCompleteUri($vector[2]);
                $vectorModel->label = $vector[1];
                $vectorModel->brand = $vector[3];
                $vectorModel->inServiceDate = $vector[6];
                $vectorModel->personInCharge = $vector[7];
                if ($vector[4] !== "") {
                    $vectorModel->serialNumber = $vector[4];
                }
                if ($vector[5] != "") {
                    $vectorModel->dateOfPurchase = $vector[5];
                }

                $forWebService[] = $vectorModel->attributesToArray();
                $insertionResult = $vectorModel->insert($sessionToken, $forWebService);
                
                $vectorsUris[] = $insertionResult->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0];
            }
            return json_encode($vectorsUris, JSON_UNESCAPED_SLASHES); 
        }
        
        return true;
    }
    
    /**
     * Searches a vector by its URI.
     * @param String $uri searched vector's URI
     * @return mixed YiiSensorModel : the searched vector
     *               "token" if the user must log in
     */
    public function findModel($uri) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $vectorModel = new YiiVectorModel();
        $requestRes = $vectorModel->findByURI($sessionToken, $uri);
        
        if ($requestRes === true) {
            return $vectorModel;
        } else if(isset($requestRes[WSConstants::TOKEN])) {
            return WSConstants::TOKEN;
        } else {
           throw new NotFoundHttpException('The requested page does not exist');
        }
    }
    
    /**
     * Lists all vectors
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new VectorSearch();
        
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
     * @action Displays a single vector model
     * @return mixed
     */
    public function actionView($id) {
        //0. Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        
        $res = $this->findModel($id);
        
        //1. get vector's linked documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documentsProvider = $searchDocumentModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], ["concernedItem" => $id]);
        
        //2. get events
        $searchEventModel = new EventSearch();
        $searchEventModel->searchConcernedItemUri = $id;
        $eventSearchParameters = [];
        if (isset($searchParams[WSConstants::EVENT_WIDGET_PAGE])) {
            $eventSearchParameters[WSConstants::PAGE] = $searchParams[WSConstants::EVENT_WIDGET_PAGE] - 1;
        }
        $eventSearchParameters[WSConstants::PAGE_SIZE] = Yii::$app->params['eventWidgetPageSize'];
        $eventsProvider = $searchEventModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $eventSearchParameters);
        $eventsProvider->pagination->pageParam = WSConstants::EVENT_WIDGET_PAGE;
        
        //3. get vector annotations
        $searchAnnotationModel = new AnnotationSearch();
        $searchAnnotationModel->targets[0] = $id;
        $annotationsProvider = $searchAnnotationModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], [AnnotationSearch::TARGET_SEARCH_LABEL => $id]);
     
        if ($res === WSConstants::TOKEN) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {            
            return $this->render('view', [
                'model' => $res,
                'dataDocumentsProvider' => $documentsProvider,
                 self::EVENTS_PROVIDER => $eventsProvider,
                 self::ANNOTATIONS_PROVIDER => $annotationsProvider
            ]);
        }
        
    }
    
    /**
     * @param array $vectorsTypes
     * @return arra list of the vectors types
     */
    private function vectorsTypesToMap($vectorsTypes) {
        $toReturn;
        foreach($vectorsTypes as $type) {
            $toReturn["http://www.opensilex.org/vocabulary/oeso#" . $type] = $type;
        }
        
        return $toReturn;
    }
    
    /**
     * @param mixed $users persons list
     * @return ArrayHelper list of the persons 'email' => 'email'
     */
    private function usersToMap($users) {
        if ($users !== null) {
            return \yii\helpers\ArrayHelper::map($users, 'email', 'email');
        } else {
            return null;
        }
    }
    
    /**
     * Updates a vector
     * @param string $id URI of the vector to update
     * @return mixed the page to show
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $model = new YiiVectorModel();
        $model->uri = $id;
        
        // if the form is complete, try to update vector
        if ($model->load(Yii::$app->request->post())) {
            
            $forWebService[] = $model->attributesToArray();
            $requestRes = $model->update($sessionToken, $forWebService);
            
            if (is_string($requestRes) && $requestRes === WSConstants::TOKEN) { //user must log in
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $model->uri]);
            }
        } else {
            $model = $this->findModel($id);
            
            // list of vector's types
            $vectorsTypes = $this->getVectorsTypes();
            if ($vectorsTypes === WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            }
        
            // list of users
            $searchUserModel = new UserSearch();
            $users = $searchUserModel->find($sessionToken, []);
            
            return $this->render('update', [
                'model' => $model,
                'types' => $this->vectorsTypesToMap($vectorsTypes),
                'users' => $this->usersToMap($users)
            ]);
        }
    }
}
