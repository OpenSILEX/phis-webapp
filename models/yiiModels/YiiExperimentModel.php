<?php

//**********************************************************************************************
//                                       YiiExperimentModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 31 2017 : passage de Trial à Experiment
// Subject: The Yii model for the Experiments. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSExperimentModel;

use Yii;

/**
 * The yii model for the experiments. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSExperimentModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiExperimentModel extends WSActiveRecord {

    /**
     * the experiment's uri
     *      (e.g http://www.phenome-fppn.fr/diaphen/DIA2017-1)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the start date of the experiment
     *      (e.g 2017-01-01)
     * @var string
     */
    public $startDate;
    const START_DATE = "startDate";
    /**
     * the end date of the experiment
     *      (e.g 2017-07-01)
     * @var string
     */
    public $endDate;
    const END_DATE = "endDate";
    /**
     * the field 
     *      (e.g Epoisses - plot F)
     * @var string
     */
    public $field;
    const FIELD = "field";
    /**
     * the campaign (corresponds to the flowering date)
     *      (e.g 2017)
     * @var string 
     */
    public $campaign;
    const CAMPAIGN = "campaign";
    /**
     * the place of the experiment
     *      (e.g. Bretenière)
     * @var string 
     */
    public $place;
    const PLACE = "place";
    /**
     * the experiment's alias used in the platform
     * @var string
     */
    public $alias;
    const ALIAS = "alias";
    /**
     * the experiment's comments
     *      (e.g. C17RAP)
     * @var string
     */
    public $comment;
    const COMMENT = "comment";
    /**
     * the experiment's description keywords
     *      (e.g Colza, azote, phénotypage, capteurs, images, variables agronomiques)
     * @var string
     */
    public $keywords;
    const KEYWORDS = "keywords";
    /**
     * the experiment's objectives 
     *      (e.g Rapsodyn- phénotypage)
     * @var string
     */
    public $objective;
    const OBJECTIVE = "objective";
    /**
     * the groups (uris) in which the experiment is. 
     *      (e.g http://www.phenome-fppn.fr/diaphen/INRA-LEPSE-DROPS)
     * @var array<string>
     */
    public $groups;
    const GROUPS = "groups";
    const GROUPS_URIS = "groupsUris";
    /**
     * the experiment's crop species. It is a string but will be replace by an
     * array of species uri when the crop species service will be done.
     *      (e.g Colza, maize)
     * @var string
     */
    public $cropSpecies;
    const CROP_SPECIES = "cropSpecies";
    /**
     * the projects (uri => acronyme) in which the experiment is. 
     *      (e.g http://www.phenome-fppn.fr/phenovia/RAPSODYN => RAPSODYN)
     * @var array<string>
     */
    public $projects;
    const PROJECTS = "projects";
    const PROJECTS_URIS = "projectsUris";
    
    /**
     * the project uri where the experiment is searched. 
     *      (e.g http://www.phenome-fppn.fr/phenovia/RAPSODYN)
     * @var string
     */
    public $projectUri;
    const PROJECT_URI = "projectUri";
    
    /**
     * the experiment's scientific supervisors contacts (email).
     *      (e.g john.doe[at]inra.fr) 
     * @var array<string>
     */
    public $scientificSupervisorContacts;
    const CONTACT_SCIENTIFIC_SUPERVISOR = "http://www.opensilex.org/vocabulary/oeso/#ScientificSupervisor";
    const CONTACT_TECHNICAL_SUPERVISOR = "http://www.opensilex.org/vocabulary/oeso/#TechnicalSupervisor";
    /**
     * the experiment's technical supervisor contacts (email).
     *      (e.g. john.doe[at]inra.fr) 
     * @var array<string>
     */
    public $technicalSupervisorContacts;
    
    const CONTACTS = "contacts";
    const CONTACT_TYPE = "type";
    /**
     * The list of the variables measured by the experiment
     * @var array<string>
     */
    public $variables;
    const VARIABLES = "variables";
    /**
     * The list of sensors which participates in the experiment
     * @var array<string>
     */
    public $sensors;
    const SENSORS = "sensors";
    
    /**
     * Initialize wsModel. In this class, wsModel is a WSExperimentModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSExperimentModel();
        ($pageSize !== null || $pageSize !== "") ? $this->pageSize = $pageSize : $this->pageSize = null;
        ($page !== null || $page !== "") ? $this->page = $page : $this->page = null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['uri', 'startDate', 'endDate', 'projects', 'campaign'], 'required'],  
          [['startDate', 'endDate', 'projects', 'groups', 'scientificSupervisorContacts', 'technicalSupervisorContacts'], 'safe'],
          [['comment', 'cropSpecies'], 'string'],
          [['uri', 'alias', 'keywords', 'objective'], 'string', 'max' => 255],
          [['field', 'campaign', 'place'], 'string', 'max' => 50],
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'uri' => 'URI',
            'startDate' => Yii::t('app', 'Date Start'),
            'endDate' => Yii::t('app', 'Date End'),
            'field' => Yii::t('app', 'Installation'),
            'campaign' => Yii::t('app', 'Campaign'),
            'place' => Yii::t('app', 'Place'),
            'alias' => 'Alias',
            'comment' => Yii::t('app', 'Comment'),
            'keywords' => Yii::t('app', 'Keywords'),
            'objective' => Yii::t('app', 'Objective'),
            'groups' => Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]),
            'projects' => Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]),
            'cropSpecies' => Yii::t('app', 'Crop Species'),
            'scientificSupervisorContacts' => Yii::t('app', 'Scientific Supervisors'),
            'technicalSupervisorContacts' => Yii::t('app', 'Technical Supervisors'),
            'variables' => Yii::t('app', 'Measured Variables'),
            'sensors' => Yii::t('app', 'Sensors which participates in')
        ];
    }
   
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     an experiment
     */
    public function arrayToAttributes($array) { 
        $this->uri = $array[YiiExperimentModel::URI];
        $this->startDate = $array[YiiExperimentModel::START_DATE];
        $this->endDate = $array[YiiExperimentModel::END_DATE];
        $this->field = $array[YiiExperimentModel::FIELD];
        $this->campaign = $array[YiiExperimentModel::CAMPAIGN];
        $this->place = $array[YiiExperimentModel::PLACE];
        $this->alias = $array[YiiExperimentModel::ALIAS];
        $this->comment = $array[YiiExperimentModel::COMMENT];
        $this->keywords = $array[YiiExperimentModel::KEYWORDS];
        $this->objective = $array[YiiExperimentModel::OBJECTIVE];
        $this->cropSpecies = $array[YiiExperimentModel::CROP_SPECIES];
        
        if (isset($array[YiiExperimentModel::PROJECTS])) {
            foreach ($array[YiiExperimentModel::PROJECTS] as $project) {
                $experimentProject[YiiProjectModel::URI] = $project->{YiiProjectModel::URI};
                $experimentProject[YiiProjectModel::ACRONYME] = $project->{YiiProjectModel::ACRONYME};
                $this->projects[] = $experimentProject;            
            }
        }
        
        if (isset($array[YiiExperimentModel::GROUPS])) {
            foreach ($array[YiiExperimentModel::GROUPS] as $group) {
                $experimentGroup[YiiGroupModel::URI] = $group->{YiiGroupModel::URI};
                $experimentGroup[YiiGroupModel::NAME] = $group->{YiiGroupModel::NAME};
                $this->groups[] = $experimentGroup;
            }
        }
        
        if (isset($array[YiiExperimentModel::CONTACTS])) {
            foreach ($array[YiiExperimentModel::CONTACTS] as $contact) {
                $experimentContact[YiiUserModel::FIRST_NAME] = $contact->{YiiUserModel::FIRST_NAME};
                $experimentContact[YiiUserModel::FAMILY_NAME] = $contact->{YiiUserModel::FAMILY_NAME};
                $experimentContact[YiiUserModel::EMAIL] = $contact->{YiiUserModel::EMAIL};
                
                if ($contact->{YiiExperimentModel::CONTACT_TYPE} === YiiExperimentModel::CONTACT_SCIENTIFIC_SUPERVISOR) {
                    $this->scientificSupervisorContacts[] = $experimentContact;
                } else {
                    $this->technicalSupervisorContacts[] = $experimentContact;
                }
            }
        }
        
        foreach ($array[YiiExperimentModel::VARIABLES] as $variableUri => $variableLabel) {
            $this->variables[$variableUri] = $variableLabel;            
        }
        
        foreach ($array[YiiExperimentModel::SENSORS] as $sensorUri => $sensorLabel) {
            $this->sensors[$sensorUri] = $sensorLabel;            
        }
    }
    
    /**
     * get experiment's informations by uri
     * @param string $sessionToken user session token
     * @param string $uri experiment's uri
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        $requestRes = $this->wsModel->getExperimentByURI($sessionToken, $uri, $params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->arrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }
    
    
    public function getExperimentsList($sessionToken) {
        $params = [];
        if ($this->pageSize !== null) {
           $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize; 
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $wsModel = new WSExperimentModel();
        $requestRes = $wsModel->getExperimentsList($sessionToken,$params);
        
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return "token";
            } else {
                return $requestRes;
            }
        } else {
            return $requestRes;
        }
    }
    
   /**
     * Create an array representing the experiment
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiExperimentModel::URI] = $this->uri;
        $elementForWebService[YiiExperimentModel::START_DATE] = $this->startDate;
        $elementForWebService[YiiExperimentModel::END_DATE] = $this->endDate;
        $elementForWebService[YiiExperimentModel::FIELD] = $this->field;
        $elementForWebService[YiiExperimentModel::CAMPAIGN] = $this->campaign;
        $elementForWebService[YiiExperimentModel::PLACE] = $this->place;
        $elementForWebService[YiiExperimentModel::ALIAS]= $this->alias;
        $elementForWebService[YiiExperimentModel::COMMENT] = $this->comment;
        $elementForWebService[YiiExperimentModel::KEYWORDS] = $this->keywords;
        $elementForWebService[YiiExperimentModel::OBJECTIVE] = $this->objective;   
        $elementForWebService[YiiExperimentModel::GROUPS] = $this->groups;
        $elementForWebService[YiiExperimentModel::PROJECTS_URIS] = $this->projects;  
        $elementForWebService[YiiExperimentModel::CROP_SPECIES] = $this->cropSpecies;

        // Project Uri is only used in case of search and not in case of posting data
        if ($this->projectUri != null) {
            $elementForWebService[YiiExperimentModel::PROJECT_URI] = $this->projectUri;          
        }
        
        if ($this->groups != null) {
            foreach ($this->groups as $groupUri) {
                $elementForWebService[YiiExperimentModel::GROUPS_URIS][] = $groupUri;
            }
        }
        
        if ($this->scientificSupervisorContacts != null) {
            foreach ($this->scientificSupervisorContacts as $scientificSupervisor) {
                $contact[YiiExperimentModel::CONTACT_TYPE] = YiiExperimentModel::CONTACT_SCIENTIFIC_SUPERVISOR;
                $contact[YiiUserModel::EMAIL] = $scientificSupervisor;
                $elementForWebService[YiiExperimentModel::CONTACTS][] = $contact;
            }
        }
        
        if ($this->technicalSupervisorContacts != null) {
            foreach ($this->technicalSupervisorContacts as $technicalSupervisor) {
                $contact[YiiExperimentModel::CONTACT_TYPE] = YiiExperimentModel::CONTACT_TECHNICAL_SUPERVISOR;
                $contact[YiiUserModel::EMAIL] = $technicalSupervisor;
                $elementForWebService[YiiExperimentModel::CONTACTS][] = $contact;
            }
        }
        
        return $elementForWebService;
    }
    
    /**
     * Update variables measured by an experiment
     * @param string $sessionToken
     * @param string $experimentUri
     * @param array $variablesUri
     * @return the query result
     */
    public function updateVariables($sessionToken, $experimentUri, $variablesUri) {
        $requestRes = $this->wsModel->putExperimentVariables($sessionToken, $experimentUri, $variablesUri);
        
        if (is_string($requestRes) && $requestRes === "token") {
            return $requestRes;
        } else if (isset($requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS})) {
            return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS};
        } else {
            return $requestRes;
        }
    }
    
    /**
     * Update sensors which participates in an experiment
     * @param string $sessionToken
     * @param string $experimentUri
     * @param array $sensorsUris
     * @return the query result
     */
    public function updateSensors($sessionToken, $experimentUri, $sensorsUris) {
        $requestRes = $this->wsModel->putExperimentSensors($sessionToken, $experimentUri, $sensorsUris);
        
        if (is_string($requestRes) && $requestRes === "token") {
            return $requestRes;
        } else if (isset($requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS})) {
            return $requestRes->{\app\models\wsModels\WSConstants::METADATA}->{\app\models\wsModels\WSConstants::STATUS};
        } else {
            return $requestRes;
        }
    }
}
