<?php

//**********************************************************************************************
//                                       YiiDocumentModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: June 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2017
// Subject: The Yii model for the Documents. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSDocumentModel;

use Yii;

/**
 * The yii model for the documents. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * Document's metadata include dublin core
 * @see http://dublincore.org/documents/dces/
 * @see app\models\wsModels\WSDocumentModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiDocumentModel extends WSActiveRecord {
    /**
     * document's uri
     *      (e.g http://www.phenome-fppn.fr/diaphen/documents/documente49c0529655c4999aa725dfa6c339eae)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * document's type
     *      (e.g http://www.phenome-fppn.fr/vocabulary/2017/#DataFile)
     * @var string
     */
    public $documentType;
    const DOCUMENT_TYPE = "documentType";
    /**
     * document's creator 
     *      (e.g John Doe)
     * @see http://purl.org/dc/elements/1.1/creator
     * @var string
     */
    public $creator;
    const CREATOR = "creator";
    /**
     * document's language
     *      (e.g fr)
     * @see http://dublincore.org/2012/06/14/dcelements#language
     * @var string
     */
    public $language;
    const LANGUAGE = "language";
    /**
     * document's title 
     *      (e.g Experimental Protocol)
     * @see http://dublincore.org/2012/06/14/dcelements#title
     * @var string
     */
    public $title;
    const TITLE = "title";
    /**
     * document's date
     *      (e.g 2017-01-01)
     * @see http://dublincore.org/2012/06/14/dcelements#date
     * @var string
     */
    public $creationDate;
    const CREATION_DATE = "creationDate";
    /**
     * document's format
     *      (e.g JPG)
     * @see http://dublincore.org/2012/06/14/dcelements#format
     * @var string
     */
    public $format;
    const FORMAT = "format";
    const EXTENSIONS = "extension";
    /**
     * list of the objects concerned by the document 
     * @var array 
     */
    public $concernedItems;
    const CONCERNED_ITEMS_URIS = "concernedItemsUris";
    /**
     * used to search document. Correspond to the element concerned by document
     * @var string
     */
    public $concernedItem; 
    const CONCERN = "concern";
    const CONCERNED_ITEM = "concernedItem";
    const CONCERNED_ITEM_RDF_TYPE = "typeURI";
    const CONCERNED_ITEM_EXPERIMENT_RDF_TYPE = "http://www.phenome-fppn.fr/vocabulary/2017#Experiment";
    const CONCERNED_ITEM_PROJECT_RDF_TYPE = "http://www.phenome-fppn.fr/vocabulary/2017#Project";
    /**
     * the file concerned by the metadata
     * @var file 
     */
    public $file;
    /**
     * list of the uris of the experiments concerned by the document.
     * (used for the user interface)
     * @var array<string>
     */
    public $concernedExperiments;
    /**
     * list of the uris of the projects concerned by the document. 
     * (used for the user interface)
     * @var array<string>
     */
    public $concernedProjects;
    /**
     * list of the uris of the sensors concerned by the document. 
     * (used for the user interface)
     * @var array<string>
     */
    public $concernedSensors;
    /**
     * the file md5sum
     * @var string
     */
    public $md5;
    const CHECKSUM = "checksum";
    /**
     * the document's comment. 
     * @var string
     */
    public $comment;
    const COMMENT = "comment";
    /**
     * the document's status.
     * "linked" if the file is linked to at least one element in the triplestore
     * "unlinked" if the file is not linked to any element in the triplestore.
     * @var string
     */
    public $status;
    const STATUS = "status";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSDocumentModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSDocumentModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['uri', 'documentType', 'creator', 'language', 'title', 'creationDate'], 'required'],
          [['uri', 'documentType', 'creator', 'language', 'title', 'creationDate', 'format', 'concernedItems', 'status', 'concernedExperiments', 'concernedProjects', 'concernedSensors', 'file', 'comment'], 'safe'],
          [['uri', 'creator', 'language', 'title', 'creationDate', 'format', 'comment'], 'string'],
          [['file'], 'file', 'skipOnEmpty' => false]
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
          'uri' => 'URI',
          'documentType' => Yii::t('app', 'Document Type'),
          'creator' => Yii::t('app', 'Creator'),
          'language' => Yii::t('app', 'Language'),
          'title' => Yii::t('app', 'Title'),
          'creationDate'=> Yii::t('app', 'Creation Date'),
          'format' => Yii::t('app', 'Format'),
//          'concernedItems' => Yii::t('app', 'Concerned Elements'),
          'file' => Yii::t('app', 'File'),
          'concernedExperiments' => Yii::t('app', 'Concerned Experimentations'),
          'concernedProjects' => Yii::t('app', 'Concerned Projects'),
          'concernedSensors' => Yii::t('app', 'Concerned Sensors'),
          'comment' => Yii::t('app', 'Comment'),
          'status' => Yii::t('app', 'Status') 
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of a document
     */
    public function arrayToAttributes($array) {
        $this->uri = $array[YiiDocumentModel::URI];
        $this->documentType = $array[YiiDocumentModel::DOCUMENT_TYPE];
        $this->creator = $array[YiiDocumentModel::CREATOR];
        $this->language = $array[YiiDocumentModel::LANGUAGE];
        $this->title = $array[YiiDocumentModel::TITLE];
        $this->creationDate = $array[YiiDocumentModel::CREATION_DATE];
        $this->format = $array[YiiDocumentModel::FORMAT];
        $this->comment = $array[YiiDocumentModel::COMMENT];
        
        if (isset($array[YiiDocumentModel::CONCERNED_ITEMS_URIS])) {
            foreach ($array[YiiDocumentModel::CONCERNED_ITEMS_URIS] as $concernedItem) {
                if ($concernedItem[YiiDocumentModel::CONCERNED_ITEM_RDF_TYPE] === YiiDocumentModel::CONCERNED_ITEM_EXPERIMENT_RDF_TYPE) {
                    $this->concernedExperiments[] = $concernedItem[YiiDocumentModel::URI];
                } else if ($concernedItem[YiiDocumentModel::CONCERNED_ITEM_RDF_TYPE] === YiiDocumentModel::CONCERNED_ITEM_PROJECT_RDF_TYPE) {
                  $this->concernedProjects[] = $concernedItem[YiiDocumentModel::URI];
                }
            }
        }
    }

    /**
     * Create an array representing the document metadata
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService[YiiDocumentModel::DOCUMENT_TYPE] = $this->documentType;
        $elementForWebService[YiiDocumentModel::URI] = $this->uri;
        $elementForWebService[YiiDocumentModel::CREATOR] = $this->creator;
        $elementForWebService[YiiDocumentModel::LANGUAGE] = $this->language;
        $elementForWebService[YiiDocumentModel::TITLE] = $this->title;
        $elementForWebService[YiiDocumentModel::CREATION_DATE] = $this->creationDate;
        $elementForWebService[YiiDocumentModel::EXTENSIONS] = $this->format; 
        $elementForWebService[YiiDocumentModel::CHECKSUM] = $this->md5;
        $elementForWebService[YiiDocumentModel::COMMENT] = $this->comment;
        $elementForWebService[YiiDocumentModel::STATUS] = $this->status;
        
        if ($this->concernedProjects != null) {
            foreach ($this->concernedProjects as $concernedProject) {
                $item[YiiDocumentModel::URI] = $concernedProject;
                $item[YiiDocumentModel::CONCERNED_ITEM_RDF_TYPE] = YiiDocumentModel::CONCERNED_ITEM_PROJECT_RDF_TYPE;
                $elementForWebService[YiiDocumentModel::CONCERN][] = $item;
            }
        }
        if ($this->concernedExperiments != null) {
            foreach ($this->concernedExperiments as $concernedExperiment) {
                $item[YiiDocumentModel::URI] = $concernedExperiment;
                $item[YiiDocumentModel::CONCERNED_ITEM_RDF_TYPE] = YiiDocumentModel::CONCERNED_ITEM_EXPERIMENT_RDF_TYPE;
                $elementForWebService[YiiDocumentModel::CONCERN][] = $item;
            }
        }
        if ($this->concernedSensors != null) {
            foreach ($this->concernedSensors as $concernedSensor) {
                $item[YiiDocumentModel::URI] = $concernedSensor;
                $sensorModel = new YiiSensorModel();
                $sensorModel->findByURI(Yii::$app->session['access_token'], $concernedSensor);       
                $item[YiiDocumentModel::CONCERNED_ITEM_RDF_TYPE] = $sensorModel->rdfType;
                $elementForWebService[YiiDocumentModel::CONCERN][] = $item;
            }
        }
        
        //SILEX:refactor
        //code to check : is that block really usefull ?
        if ($this->concernedItems !== null) {
            foreach ($this->concernedItems as $concernedItem) {
                $item[YiiDocumentModel::URI] = $concernedItem;
                $elementForWebService[YiiDocumentModel::CONCERN][] = $item;
            }
        }
        
        if ($this->concernedItem !== null) {
            $elementForWebService[YiiDocumentModel::CONCERNED_ITEM] = $this->concernedItem;
        }
        //\SILEX:refactor
        
        return $elementForWebService;
    }
    
    /**
     * find all the document's types of the database 
     * @param string $sessionToken the user session token
     * @return array list of the document's types
     */
    public function findDocumentsTypes($sessionToken)  {       
        $requestRes = $this->wsModel->getTypes($sessionToken);
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * get a document by it's uri
     * @param string $sessionToken the user session token
     * @param string $documentUri the document's uri 
     * @param string $format the file extension
     * @return string|array \app\models\wsModels\WSConstants::TOKEN if the user must log in
     *                      array with the document
     */
    public function getDocument($sessionToken, $documentUri, $format) {
        $requestRes = $this->wsModel->getFileByURI($sessionToken, $documentUri, $format);
        
        if (is_array($requestRes) && isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
            return \app\models\wsModels\WSConstants::TOKEN;
        } else {
            return $requestRes;
        }        
    }
    
    /**
     * create a document
     * @param string $sessionToken the user session token
     * @param file $document the document to send to the web service 
     * @return mixed post result
     */
    public function postDocument($sessionToken, $document, $requestURL) {
        $requestRes = $this->wsModel->postFile($sessionToken, $document, $requestURL);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES})) {
                return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::DATA_FILES};
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
    /**
     * find a document by it's uri and fill the attributes of "this"
     * @param string $sessionToken the user session token
     * @param string $uri the document uri
     */
    public function findByURI($sessionToken, $uri) {
        $search[YiiDocumentModel::URI] = $uri;
        $res = $this->find($sessionToken, $search);

        $this->uri = $res[0]->uri;
        $this->title = $res[0]->title;
        $this->documentType = $res[0]->documentType;
        $this->creator = $res[0]->creator;
        $this->language = $res[0]->language;
        $this->creationDate = $res[0]->creationDate;
        $this->format = $res[0]->format;
        $this->comment = $res[0]->comment;
        foreach ($res[0]->concernedItems as $concernedItem) {
            if ($concernedItem->typeURI === YiiDocumentModel::CONCERNED_ITEM_EXPERIMENT_RDF_TYPE) {
                $this->concernedExperiments[] = $concernedItem->uri;
            } else if ($concernedItem->typeURI === YiiDocumentModel::CONCERNED_ITEM_PROJECT_RDF_TYPE) {
              $this->concernedProjects[] = $concernedItem->uri;
            }
        }
    }
}
