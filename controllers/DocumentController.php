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
use app\models\yiiModels\ProjectSearch;
use app\models\yiiModels\ExperimentSearch;
use app\models\yiiModels\DocumentSearch;

require_once '../config/config.php';

/**
 * CRUD actions for YiiDocumentModel
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiDocumentModel
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DocumentController extends Controller {
    
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
     * 
     * @param mixed $projects 
     * @return ArrayHelper uri => name
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
     * @param mixed $experiments 
     * @return ArrayHelper uri => alias
     */
    private function experimentsToMap($experiments) {
        if ($experiments !== null) {
            return \yii\helpers\ArrayHelper::map($experiments, 'uri', 'alias');
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
                if ($concernedItem->typeURI === "http://www.phenome-fppn.fr/vocabulary/2017#Experiment") {
                    $documentModel->concernedExperiments[] = $concernedItem->uri;
                } else if ($concernedItem->typeURI === "http://www.phenome-fppn.fr/vocabulary/2017#Project") {
                  $documentModel->concernedProjects[] = $concernedItem->uri;
                }
                $documentModel->concernedItems[] = $concernedItem;
            }
            
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
        $searchResult = $searchModel->search(Yii::$app->session['access_token'], Yii::$app->request->queryParams);    
        
        if (is_string($searchResult)) {
            return $this->render('/site/error', [
                    'name' => 'Internal error',
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
     * @action creates a document
     * @return mixed
     */
    public function actionCreate($concernedItem = null) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel(null, null);
        
        //if the form is complete, try to save the data
        if ($documentModel->load(Yii::$app->request->post())) {
            
            if ($concernedItem !== null) {
                $documentModel->concernedItems[] = $concernedItem;
            }
            $documentModel->status = "linked";
            
            $documentModel->isNewRecord = true;
            
            //1. register document
            $document = UploadedFile::getInstance($documentModel, 'file');
            $format = explode(".", $document->name);
            $documentModel->format = $format[count($format)-1];
            $serverFilePath = \config::path()['documentsUrl'] . $document->name;

            $document->saveAs($serverFilePath);
            $documentModel->md5 = md5_file($serverFilePath);
            
            //2. send metadata
            $dataToSend[] = $documentModel->attributesToArray();
            
            $requestRes = $documentModel->insert($sessionToken, $dataToSend);
            
            $requestURL = isset($requestRes->metadata->datafiles) ? $requestRes->metadata->datafiles[0] : null;
            
            
            //3. send file
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
                return $this->render('create', [
                   'model' => $documentModel 
                ]);
            }
        } else {
            //1. get document's types
            $documentsTypes = $documentModel->findDocumentsTypes($sessionToken);
            
            //2. get experiments
            $searchExperimentModel = new ExperimentSearch();
            $experiments = $searchExperimentModel->find($sessionToken, []);
            
            //3. get projects
            $searchProjectModel = new ProjectSearch();
            $projects = $searchProjectModel->find($sessionToken,[]);
            
            //4. get actual concerned item (if there is already a concerned document)
            $actualConcernedItem[] = $concernedItem;
            
            if (is_string($projects) || is_string($experiments)) {
                return $this->render('/site/error', [
                    'name' => 'Internal error',
                    'message' => is_string($projects) ? $projects : $experiments]);
            } else if (is_array($projects) && isset($projects["token"]) 
                    || is_array($experiments) && isset($experiments["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $experiments = $this->experimentsToMap($experiments);
                $documentsTypes = $this->documentsTypesToMap($documentsTypes);
                $this->view->params['listProjects'] = $projects;
                $this->view->params['listExperiments'] = $experiments;
                $this->view->params['listDocumentsTypes'] = $documentsTypes;
//                $this->view->params['concernedItem'] = $concernedItem;
                $this->view->params['actualConcernedItem'] = $actualConcernedItem;
                $documentModel->isNewRecord = true;
                                
                return $this->render('create', [
                   'model' => $documentModel,
                ]);
            }
        }
    }
    
    /**
     * 
     * @param string $id document to update's uri
     * @return mixed
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session['access_token'];
        $documentModel = new YiiDocumentModel(null, null);
        
        if ($documentModel->load(Yii::$app->request->post())) {
           
            $documentModel->isNewRecord = true;
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
                if ($concernedItem->typeURI === "http://www.phenome-fppn.fr/vocabulary/2017#Experiment") {
                    $documentModel->concernedExperiments[] = $concernedItem->uri;
                } else if ($concernedItem->typeURI === "http://www.phenome-fppn.fr/vocabulary/2017#Project") {
                  $documentModel->concernedProjects[] = $concernedItem->uri;
                }
            }
            //\SILEX:todo
            
            //1. Get type documents list
            $documentsTypes = $documentModel->findDocumentsTypes($sessionToken);
            
            //2. get experiments
            $searchExperimentModel = new ExperimentSearch();
            $experiments = $searchExperimentModel->find($sessionToken, []);
            
            //3. get projets
            $searchProjectModel = new ProjectSearch();
            $projects = $searchProjectModel->find($sessionToken,[]);
            
            if (is_string($projects) || is_string($experiments)) {
                return $this->render('/site/error', [
                    'name' => 'Internal error',
                    'message' => is_string($projects) ? $projects : $experiments]);
            } else if (is_array($projects) && isset($projects["token"]) 
                    || is_array($experiments) && isset($experiments["token"])) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                $projects = $this->projectsToMap($projects);
                $experiments = $this->experimentsToMap($experiments);
                $documentsTypes = $this->documentsTypesToMap($documentsTypes);
                $this->view->params['listProjects'] = $projects;
                $this->view->params['listExperiments'] = $experiments;
                $this->view->params['listDocumentsTypes'] = $documentsTypes;
                $this->view->params['concernedItem'] = null;
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
                    'name' => 'Internal error',
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
