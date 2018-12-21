<?php

//**********************************************************************************************
//                                       DocumentController.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2017
// Subject: implements the CRUD for the documents (ws document model)
//***********************************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use yii\web\UploadedFile;

use app\models\yiiModels\YiiDocumentModel;
use app\models\yiiModels\YiiSensorModel;
use app\models\yiiModels\YiiVectorModel;
use app\models\yiiModels\DocumentSearch;

require_once '../config/config.php';

/**
 * CRUD actions for YiiDocumentModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDocumentModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 * @update [Morgane Vidal] 10 August, 2018 : add link documents to sensors and vectors
 */
class DocumentController extends Controller {
    
    //The following constants are used to get some concepts URI from the Yii params.
    // (e.g. Yii::$app->params[DocumentController::PROJECT])
    const PROJECT = "Project";
    const EXPERIMENT = "Experiment";
    const INSTALLATION = "Installation";
    const RADIOMETRIC_TARGET = "RadiometricTarget";
    
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
     * @param mixed $concerns 
     * @return ArrayHelper uri => label
     */
    private function concernsToMap($concerns) {
        if ($concerns !== null) {
            return \yii\helpers\ArrayHelper::map($concerns, 'uri', 'label');
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
                $concern[YiiDocumentModel::URI] = $concernedItem->uri;
                
                //Get the type for interface urls
                if ($concernedItem->typeURI === Yii::$app->params[DocumentController::EXPERIMENT]) {
                    $concern["type"] = "experiment";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::PROJECT]) {
                    $concern["type"] = "project";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::INSTALLATION]) {
                    $concern["type"] = "infrastructure";
                } else if ($concernedItem->typeURI === Yii::$app->params[DocumentController::RADIOMETRIC_TARGET]) {
                    $concern["type"] = "radiometric-target";
                }else {
                    //check if a sensor or a vector 
                    $sensorModel = new YiiSensorModel();
                    $requestRes = $sensorModel->findByURI($sessionToken, $concernedItem->uri);
                    if ($requestRes && $sensorModel->uri === $concernedItem->uri) {
                        $concern["type"] = "sensor";
                    } else {
                        $vectorModel = new YiiVectorModel();
                        $requestRes = $vectorModel->findByURI($sessionToken, $concernedItem->uri);
                        if ($requestRes && $vectorModel->uri === $concernedItem->uri) {
                            $concern["type"] = "vector";
                        }
                    }
                }
                
                $documentModel->concernedItems[] = $concern;
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
     * @param $concernUri The URI of the target concerned by the document.
     * @param $concernLabel The label of the target concerned by the document. 
     *                      Used for the create document interface.
     * @param $concernRdfType The type of the target concerned by the document.
     * @return mixed the creation form, the message error or the view of the 
     *               document created
     */
    public function actionCreate($concernUri = null, $concernLabel = null, $concernRdfType = null) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel(null, null);
        
        //If the form is complete, try to save the data
        if ($documentModel->load(Yii::$app->request->post())) {
            //1. Set metadata
            //1.1 Add the rdf type of the concerns
            $wsUriModel = new \app\models\wsModels\WSUriModel();
            $concerns = null;

            //SILEX:info
            //Code to uncomment when the functionality of adding multiple concern to a document will be done
//            foreach ($documentModel->concernedItems as $concern) {
//                $item = new \app\models\yiiModels\YiiInstanceDefinitionModel();
//                $item->uri = $concern;
//                
//                //get concern rdfType by call to the web service
//                $rdfType = $wsUriModel->getUriType(Yii::$app->session['access_token'], $concern, null);
//                $item->rdfType = $rdfType;
//                $concerns[] = $item;
//            }
            //\SILEX:info
            
            //Prepare the target of the document
            if ($concernUri !== null) {
                $item = new \app\models\yiiModels\YiiInstanceDefinitionModel();
                $item->uri = $concernUri;
                
                if ($concernRdfType === null) {
                    //Get concern rdfType
                    $rdfType = $wsUriModel->getUriType(Yii::$app->session['access_token'], $concernUri, null);
                    $item->rdfType = $rdfType;
                } else {
                    $item->rdfType = $concernRdfType;
                }
                
                $concerns[] = $item;
            }
            
            $documentModel->concernedItems = $concerns;
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
                    $documentUri = $requestRes[0];
                    return $this->redirect(['view', 'id' => $documentUri]);
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
                $concern = new \app\models\yiiModels\YiiInstanceDefinitionModel();
                $concerns = null;
                $concern->uri = $concernUri;
                $concern->label = $concernLabel;
                $concerns[] = $concern;
                
                $documentsTypes = $this->documentsTypesToMap($documentsTypes);
                $this->view->params['listDocumentsTypes'] = $documentsTypes;
                $this->view->params['actualConcerns'] = $this->concernsToMap($concerns);
                $documentModel->concernedItems[] = $concernUri;
                
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
     * This is because we cannot update the concerns of the document yet.
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
            $concernedDecode = json_decode($concernedItems, JSON_UNESCAPED_SLASHES);
            foreach ($concernedDecode as $concern) {
                $concernToSave = new \app\models\yiiModels\YiiInstanceDefinitionModel();
                $concernToSave->uri = $concern["uri"];
                $concernToSave->rdfType = $concern["typeURI"];
                $documentModel->concernedItems[] = $concernToSave;
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
}
