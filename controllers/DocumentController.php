<?php

//**********************************************************************************************
//                                       DocumentController.php 
// PHIS-SILEX
// Copyright © INRA 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use yii\web\UploadedFile;

use app\models\wsModels\WSUriModel;
use app\models\yiiModels\YiiSensorModel;
use app\models\yiiModels\YiiVectorModel;
use app\models\yiiModels\YiiModelsConstants;
use app\models\yiiModels\YiiInstanceDefinitionModel;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiDocumentModel;
use yii\grid\GridView;
use yii\helpers\Html;

require_once '../config/config.php';

/**
 * CRUD actions for YiiDocumentModel
 * @update [Morgane Vidal] 10 August, 2018: add link documents to sensors and vectors
 * @update [Andréas Garcia] 15 Jan., 2019: change "concern" occurences to "concernedItem"
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDocumentModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DocumentController extends Controller {
    
    //The following constants are used to get some concepts URI from the Yii params.
    // (e.g. Yii::$app->params[DocumentController::PROJECT])
    const PROJECT = "Project";
    const EXPERIMENT = "Experiment";
    const INSTALLATION = "Installation";
    const RADIOMETRIC_TARGET = "RadiometricTarget";
    const ACTUATOR = "Actuator";
    
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
     * Generates a map uri => label
     * @param mixed $concernedItems 
     * @return ArrayHelper uri => label
     */
    private function concernedItemsToMap($concernedItems) {
        if ($concernedItems !== null) {
            return \yii\helpers\ArrayHelper::map($concernedItems, 'uri', 'label');
        } else {
            return null;
        }
    }
    
    /**
     * 
     * @param mixed $documentsTypes
     * @return ArrayHelper uri => type
     */
    private function documentsTypesToMap($documentsTypes) {
        $types = null;
        foreach ($documentsTypes as $documentType) {
            $elements = explode("#", $documentType);
            $types[$documentType] = $elements[1];
        }
        
        return $types;
    }
    
    /**
     * document view
     * @param string $id document uri
     * @return mixed
     */
    public function actionView($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel();
        
        //calls search document to get metadata
        $search["uri"] = $id;
        $res = $documentModel->find($sessionToken, $search);
  
        if (isset($res["token"])) {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            $documentModel->uri = $res[0]->uri;
            $documentModel->title = $res[0]->title;
            $documentModel->documentType = $res[0]->documentType;
            $documentModel->creator = $res[0]->creator;
            $documentModel->language = $res[0]->language;
            $documentModel->creationDate = $res[0]->creationDate;
            $documentModel->format = $res[0]->format;
            $documentModel->comment = $res[0]->comment;
            foreach ($res[0]->concernedItems as $concernedItem) {
                $concernedItems[YiiDocumentModel::URI] = $concernedItem->uri;
                
                //Get the type for interface urls
                if ($concernedItem->typeURI === Yii::$app->params[DocumentController::EXPERIMENT]) {
                    $concernedItems["type"] = "experiment";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::PROJECT]) {
                    $concernedItems["type"] = "project";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::INSTALLATION]) {
                    $concernedItems["type"] = "infrastructure";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::RADIOMETRIC_TARGET]) {
                    $concernedItems["type"] = "radiometric-target";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::ACTUATOR]) {
                   $concernedItems["type"] = "actuator"; 
                } else {
                    //check if a sensor or a vector 
                    $sensorModel = new YiiSensorModel();
                    $requestRes = $sensorModel->findByURI($sessionToken, $concernedItem->uri);
                    if ($requestRes && $sensorModel->uri === $concernedItem->uri) {
                        $concernedItems["type"] = "sensor";
                    } else {
                        $vectorModel = new YiiVectorModel();
                        $requestRes = $vectorModel->findByURI($sessionToken, $concernedItem->uri);
                        if ($requestRes && $vectorModel->uri === $concernedItem->uri) {
                            $concernedItems["type"] = "vector";
                        }
                    }
                }
                
                $documentModel->concernedItems[] = $concernedItems;
            }
            $this->view->params['listRealConcernedItems'] = $res[0]->concernedItems;
            
            return $this->render('view', [
                'model' => $documentModel,
            ]);
        }
    }

    /**
     * index page
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new DocumentSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;        
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $searchParams[YiiModelsConstants::PAGE]--;
        }
        
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);    
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN) {
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
     * @param string $id uri of the document to upload
     * @return mixed
     */
    public function actionDownload($id, $format) {
        $documentModel = new YiiDocumentModel();
        $requestRes = $documentModel->getDocument(Yii::$app->session['access_token'], $id, $format);
        
        if (is_string($requestRes) && $requestRes === "token") {
            return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
        } else {
            if (file_exists($requestRes)) {
                Yii::$app->response->sendFile($requestRes)->send();
                unlink($requestRes);
            } else {
                echo "file does not exist : " . $requestRes;
            }
        }
    }
    
    /**
     * Creates a document
     * @param $concernedItemUri The URI of the target concerned by the document.
     * @param $concernedItemLabel The label of the target concerned by the document. 
     *                      Used for the create document interface.
     * @param $concernedItemRdfType The type of the target concerned by the document.
     * @return mixed the creation form, the message error or the view of the 
     *               document created
     */
    public function actionCreate($concernedItemUri = null, $concernedItemLabel = null, $concernedItemRdfType = null, $returnUrl = null) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel(null, null);
        
        if ($returnUrl) {
            $documentModel->returnUrl = $returnUrl;
        }
        
        //If the form is complete, try to save the data
        if ($documentModel->load(Yii::$app->request->post())) {
            //1. Set metadata
            //1.1 Add the RDF type of the concerned items
            $wsUriModel = new WSUriModel();
            $concernedItems = null;

            //SILEX:info
            //Code to uncomment when the functionality of adding multiple concerned items to a document is done
//            foreach ($documentModel->concernedItems as $concernedItem) {
//                $item = new \app\models\yiiModels\YiiInstanceDefinitionModel();
//                $item->uri = $concernedItem;
//                
//                //get concerned item rdfType by call to the web service
//                $rdfType = $wsUriModel->getUriType(Yii::$app->session['access_token'], $concernedItem, null);
//                $item->rdfType = $rdfType;
//                $concernedItems[] = $item;
//            }
            //\SILEX:info
            
            //Prepare the target of the document
            if ($concernedItemUri !== null) {
                $concernedItem = new YiiInstanceDefinitionModel();
                $concernedItem->uri = $concernedItemUri;
                if ($concernedItemRdfType === null) {
                    //Get concerned item rdfType
                    $rdfType = $wsUriModel->getUriType(Yii::$app->session['access_token'], $concernedItemUri, null);
                    $concernedItem->rdfType = $rdfType;
                } else {
                    $concernedItem->rdfType = $concernedItemRdfType;
                }
                
                $concernedItems[] = $concernedItem;
            }
            
            $documentModel->concernedItems = $concernedItems;
            $documentModel->status = "linked";
            $documentModel->isNewRecord = true;
            
            //2. Register document
            $document = UploadedFile::getInstance($documentModel, 'file');
            $format = explode(".", $document->name);
            $documentModel->format = $format[count($format)-1];
            $serverFilePath = \config::path()['documentsUrl'] . $document->name;

            $document->saveAs($serverFilePath);
            $documentModel->md5 = md5_file($serverFilePath);
            
            //3. Send metadata
            $dataToSend[] = $documentModel->attributesToArray();
            
            $requestRes = $documentModel->insert($sessionToken, $dataToSend);
            
            $requestURL = isset($requestRes->metadata->datafiles) ? $requestRes->metadata->datafiles[0] : null;
            
            //4. Send file
            if ($requestURL !== null) {
                $file = fopen($serverFilePath, 'r');
                $requestRes = $documentModel->postDocument($sessionToken, $file, $requestURL);   
                unlink($serverFilePath);
                if (is_string($requestRes) && $requestRes === "token") {
                    return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                } else {
                    if ($documentModel->returnUrl) {
                        $this->redirect($documentModel->returnUrl);
                    } else {
                        $documentUri = $requestRes[0];
                        return $this->redirect(['view', 'id' => $documentUri]);
                    }
                }
            } else {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => Yii::t('app/messages', 'An error occurred.')]);
            }
        } else {
            //Get document's types
            $documentsTypes = $documentModel->findDocumentsTypes($sessionToken);
            
            if (is_string($documentsTypes)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $documentsTypes]);
            } else if (is_array($documentsTypes) && isset($documentsTypes["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $concernedItem = new YiiInstanceDefinitionModel();
                $concernedItems = null;
                $concernedItem->uri = $concernedItemUri;
                $concernedItem->label = $concernedItemLabel;
                $concernedItems[] = $concernedItem;
                
                $documentsTypes = $this->documentsTypesToMap($documentsTypes);
                $this->view->params['listDocumentsTypes'] = $documentsTypes;
                $this->view->params['currentConcernedItem'] = $this->concernedItemsToMap($concernedItems);
                $documentModel->concernedItems[] = $concernedItemUri;
                
                $documentModel->isNewRecord = true;
                                
                return $this->render('create', [
                   'model' => $documentModel,
                ]);
            }
        }
    }
    
    /**
     * Update a document 
     * @param string $id URI of the document to update.
     * @param json $concernedItems The list of the actual concerned items for the document.
     * This is because we cannot update the concerned items of the document yet.
     * @return mixed the view, the login page, the error message or the update form
     */
    public function actionUpdate($id, $concernedItems = null) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel(null, null);
        
        if ($documentModel->load(Yii::$app->request->post())) {
            //SILEX:info
            //Remove the following code and update with the list of the concerned 
            //items when the functionality will be added
            //The list of the concerned items cannot be updated yet
            $concernedItemsDecoded = json_decode($concernedItems, JSON_UNESCAPED_SLASHES);
            foreach ($concernedItemsDecoded as $concernedItem) {
                $concernedItemToSave = new YiiInstanceDefinitionModel();
                $concernedItemToSave->uri = $concernedItem["uri"];
                $concernedItemToSave->rdfType = $concernedItem["typeURI"];
                $documentModel->concernedItems[] = $concernedItemToSave;
            }
            //\SILEX:info
            
            $documentModel->isNewRecord = false;
            $documentModel->uri = $id;
            
            $documentModel->status = "linked";
            $dataToSend[] = $documentModel->attributesToArray();
            
            $requestRes = $documentModel->update($sessionToken, $dataToSend);
            
            if (is_string($requestRes) && $requestRes === "token") { //L'utilisateur doit se connecter
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->redirect(['view', 'id' => $documentModel->uri]);
            }
        } else { 
            //get document's metadata
            //SILEX:todo
            //use $model->findByURI($sessionToken, $uri) instead of this code block
            $search["uri"] = $id;
            $res = $documentModel->find($sessionToken, $search);            
            
            $documentModel->uri = $res[0]->uri;
            $documentModel->title = $res[0]->title;
            $documentModel->documentType = $res[0]->documentType;
            $documentModel->creator = $res[0]->creator;
            $documentModel->language = $res[0]->language;
            $documentModel->creationDate = $res[0]->creationDate;
            $documentModel->format = $res[0]->format;
            $documentModel->comment = $res[0]->comment;
            $documentModel->status = $res[0]->status;
            foreach ($res[0]->concernedItems as $concernedItem) {
                $documentModel->concernedItems[] = $concernedItem;
            }
            //\SILEX:todo
            
            //Get type documents list
            $documentsTypes = $documentModel->findDocumentsTypes($sessionToken);
            
            if (is_string($documentsTypes)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $documentsTypes]);
            } else if (is_array($documentsTypes) && isset($documentsTypes["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $documentsTypes = $this->documentsTypesToMap($documentsTypes);
                $this->view->params['listDocumentsTypes'] = $documentsTypes;
                $documentModel->isNewRecord = false;
                        
                return $this->render('update', [
                   'model' => $documentModel 
                ]);
            }
        }
    }
    
    /**
     * create documents for dataset (documents are associated to dataset)
     */
    public function actionCreateFromDataset() {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel();
        if ($documentModel->load(Yii::$app->request->post())) {
            
            //SILEX:info
            // UploadedFile does not work here. This is a bug to fix
            //\SILEX:info
             //1. register document
            $uploadDir = \config::path()['documentsUrl'];
            $serverFilePath = $uploadDir . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $serverFilePath)) {
                $documentModel->md5 = md5_file($serverFilePath);
                $documentModel->status = "unlinked";
                
                //2. send metadata
                $dataToSend[] = $documentModel->attributesToArray();
                $requestRes = $documentModel->insert($sessionToken, $dataToSend);
                $requestURL = isset($requestRes->metadata->datafiles) ? $requestRes->metadata->datafiles[0] : null;
                
                //3. send document
                if ($requestURL !== null) {
                    $file = fopen($serverFilePath, 'r');
                    $requestRes = $documentModel->postDocument($sessionToken, $file, $requestURL);   
                    unlink($serverFilePath);
                    if (is_string($requestRes) && $requestRes === "token") {
                        return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
                    } else {
                        $documentUri = $requestRes[0];
                        return $documentUri;
                    }
                }                 
            } else {
            //SILEX:todo
            //flash message when error
            //\SILEX:todo
            }
            
        } else {
             //1. get documents types
            $documentsTypes = $documentModel->findDocumentsTypes($sessionToken);

            //SILEX:todo
            //get prov uri and maj documents
            //\SILEX:todo

            if (is_string($documentsTypes)) {
                return $this->render('/site/error', [
                    'name' => Yii::t('app/messages','Internal error'),
                    'message' => $documentsTypes]);
            } else if (is_array($documentsTypes) && isset($documentsTypes["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $documentsTypes = $this->documentsTypesToMap($documentsTypes);
               
                $this->view->params['listDocumentsTypes'] = $documentsTypes;
                $documentModel->isNewRecord = true;
            }

            return $this->renderAjax('_form_dataset', [
                            'model' => $documentModel,
                ]);
        }
    }
    
    /**
     * Ajax action to get the document widget of a given URI
     * @return type
     */
    public function actionGetDocumentsWidget() {
        $searchDocumentModel = new DocumentSearch();
        $post = Yii::$app->request->post();
        $searchDocumentModel->concernedItemFilter = $post['uri'];
        $documents = $searchDocumentModel->search(Yii::$app->session['access_token'], ["concernedItem" => $uri]);
        
        return GridView::widget([
            'dataProvider' => $documents,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'title',
                'creator',
                'creationDate',
                'language',
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>', 
                                ['document/view', 'id' => $model->uri],
                                ["target" => "_blank"]);
                        },
                    ]
                ],
            ]
        ]);;
    }
}
