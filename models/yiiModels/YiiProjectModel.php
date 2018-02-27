<?php

//**********************************************************************************************
//                                       YiiProjectModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: March 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  March, 2017
// Subject: The Yii model for the Projects. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSProjectModel;

use Yii;

/**
 * The yii model for the projects. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSProjectModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiProjectModel extends WSActiveRecord {

    /**
     * the project's uri
     *  (e.g. http://www.phenome-fppn.fr/diaphen/DROPS)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the project's name
     *  (e.g. DROught-tolerant yielding PlantS)
     * @var string
     */
    public $name;
    const NAME = "name";
    /**
     * the project's acronyme. Used to generate the project's uri
     *  (e.g. DROPS)
     * @var string
     */
    public $acronyme;
    const ACRONYME = "acronyme";
    /**
     * the subproject type
     *  (e.g. http://www.phenome-fppn.fr/vocabulary/2017#CDD)
     * @var string
     */
    public $subprojectType;
    const SUBPROJECT_TYPE = "subprojectType";
    public $financialSupport;
    const FINANCIAL_SUPPORT = "financialSupport";
    public $financialName;
    const FINANCIAL_NAME = "financialName";
    public $dateStart;
    const DATE_START = "dateStart";
    public $dateEnd;
    const DATE_END = "dateEnd";
    public $keywords;
    const KEYWORDS = "keywords";
    public $description;
    const DESCRIPTION = "description";
    public $objective;
    const OBJECTIVE = "objective";
    public $parentProject;
    const PARENT_PROJECT = "parentProject";
    public $website;
    const WEBSITE = "website";
    const CONTACTS = "contacts";
    const CONTACT_TYPE = "type";
    const CONTACT_SCIENTIFIC_CONTACT = "http://www.phenome-fppn.fr/vocabulary/2017/#ScientificContact";
    const CONTACT_PROJECT_COORDINATOR = "http://www.phenome-fppn.fr/vocabulary/2017/#ProjectCoordinator";
    const CONTACT_ADMINISTRATIVE_CONTACT = "http://www.phenome-fppn.fr/vocabulary/2017/#AdministrativeContact";
    public $scientificContacts;
    public $administrativeContacts;
    public $projectCoordinatorContacts;
    
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSProjectModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }
    
    public function rules() {
        return [
           [['uri', 'dateStart', 'dateEnd', 'name', 'acronyme'], 'required'],
           [['uri', 'name', 'acronyme', 'dateStart', 'dateEnd', 
               'scientificContacts', 'administrativeContacts', 'projectCoordinatorContacts'], 'safe'],
           [['description'], 'string'],
           [['uri', 'parentProject', 'website'], 'string', 'max' => 300],
           [['keywords'], 'string', 'max' => 500],
           [['name', 'acronyme', 'subprojectType', 'financialSupport', 'financialName'], 'string', 'max' => 200],
           [['objective'], 'string', 'max' => 256],
           [['type'], 'string', 'max' => 100]
        ];
    }
    
    public function attributeLabels() {
        return [
          'uri' => 'URI',
          'name' => Yii::t('app', 'Name'),
          'acronyme' => 'Acronyme',
          'subprojectType' => Yii::t('app', 'Subproject Type'),
          'financialSupport' => Yii::t('app', 'Financial Support'),
          'financialName' => Yii::t('app', 'Financial Name'),
          'dateStart' => Yii::t('app', 'Date Start'),
          'dateEnd' => Yii::t('app', 'Date End'),
          'keywords' => Yii::t('app', 'Keywords'),
          'description' => 'Description',
          'objective' => Yii::t('app', 'Objective'),
          'parentProject' => Yii::t('app', 'Subproject Of'),
          'website' => Yii::t('app', 'Website'),
          'scientificContacts' => Yii::t('app', 'Scientific Contacts'),
          'administrativeContacts' => Yii::t('app', 'Administrative Contacts'), 
          'projectCoordinatorContacts' => Yii::t('app', 'Project Coordinators'),  
        ];
    }
    
    /**
     * Permet de remplir les attributs en fonction des informations 
     * comprises dans le tableau passé en paramètre
     * @param array $array tableau clé => valeur contenant les valeurs des attributs du projet
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiProjectModel::URI];
        $this->name = $array[YiiProjectModel::NAME];
        $this->acronyme = $array[YiiProjectModel::ACRONYME];
        $this->subprojectType = $array[YiiProjectModel::SUBPROJECT_TYPE];
        $this->financialSupport = $array[YiiProjectModel::FINANCIAL_SUPPORT];
        $this->financialName = $array[YiiProjectModel::FINANCIAL_NAME];
        $this->dateStart = $array[YiiProjectModel::DATE_START];
        $this->dateEnd = $array[YiiProjectModel::DATE_END];
        $this->keywords = $array[YiiProjectModel::KEYWORDS];
        $this->description = $array[YiiProjectModel::DESCRIPTION];
        $this->objective = $array[YiiProjectModel::OBJECTIVE];
        $this->parentProject = $array[YiiProjectModel::PARENT_PROJECT];
        $this->website = $array[YiiProjectModel::WEBSITE];
        
        if (isset($array[YiiProjectModel::CONTACTS])) {
            foreach ($array[YiiProjectModel::CONTACTS] as $contact) {
                $projectContact[YiiUserModel::FIRST_NAME] = $contact->{YiiUserModel::FIRST_NAME};
                $projectContact[YiiUserModel::FAMILY_NAME] = $contact->{YiiUserModel::FAMILY_NAME};
                $projectContact[YiiUserModel::EMAIL] = $contact->{YiiUserModel::EMAIL};

                if ($contact->{YiiProjectModel::CONTACT_TYPE} === YiiProjectModel::CONTACT_SCIENTIFIC_CONTACT) {
                    $this->scientificContacts[] = $projectContact;
                } else if ($contact->{YiiProjectModel::CONTACT_TYPE} === YiiProjectModel::CONTACT_ADMINISTRATIVE_CONTACT) {
                    $this->administrativeContacts[] = $projectContact;
                } else {
                    $this->projectCoordinatorContacts[] = $projectContact;
                }
            }
        }
    }
    
    /**
     * @return array contenant l'élément à enregistrer en base de données
     *         cette méthode est publique pour que l'utilisateur puisse choisir de l'utiliser 
     *         ou d'envoyer lui-même son propre tableau (dans le cas où il souhaite enregistrer plusieurs instances)
     */
    public function attributesToArray() {
        $elementForWebService[YiiProjectModel::URI] = $this->uri;
        $elementForWebService[YiiProjectModel::NAME] = $this->name;
        $elementForWebService[YiiProjectModel::ACRONYME]= $this->acronyme;
        $elementForWebService[YiiProjectModel::SUBPROJECT_TYPE] = $this->subprojectType;
        $elementForWebService[YiiProjectModel::FINANCIAL_SUPPORT] = $this->financialSupport;
        $elementForWebService[YiiProjectModel::FINANCIAL_NAME] = $this->financialName;
        $elementForWebService[YiiProjectModel::DATE_START] = $this->dateStart;
        $elementForWebService[YiiProjectModel::DATE_END] = $this->dateEnd;
        $elementForWebService[YiiProjectModel::KEYWORDS] = $this->keywords;
        $elementForWebService[YiiProjectModel::DESCRIPTION] = $this->description;
        $elementForWebService[YiiProjectModel::OBJECTIVE] = $this->objective;
        $elementForWebService[YiiProjectModel::PARENT_PROJECT] = $this->parentProject;
        $elementForWebService[YiiProjectModel::WEBSITE] = $this->website;
        
        if ($this->administrativeContacts != null) {
            foreach ($this->administrativeContacts as $administrativeContact) {
               $contact[YiiProjectModel::CONTACT_TYPE] = YiiProjectModel::CONTACT_ADMINISTRATIVE_CONTACT;
               $contact[YiiUserModel::EMAIL] = $administrativeContact;
               $elementForWebService[YiiProjectModel::CONTACTS][] = $contact;
            }
        }
        
        if ($this->projectCoordinatorContacts != null) {
            foreach ($this->projectCoordinatorContacts as $projectCoordinator) {
                $contact[YiiProjectModel::CONTACT_TYPE] = YiiProjectModel::CONTACT_PROJECT_COORDINATOR;
                $contact[YiiUserModel::EMAIL] = $projectCoordinator;
                $elementForWebService[YiiProjectModel::CONTACTS][] = $contact;
            }
        }
        
        if ($this->scientificContacts != null) {
            foreach ($this->scientificContacts as $scientificContact) {
               $contact[YiiProjectModel::CONTACT_TYPE] = YiiProjectModel::CONTACT_SCIENTIFIC_CONTACT;
               $contact[YiiUserModel::EMAIL] = $scientificContact;
               $elementForWebService[YiiProjectModel::CONTACTS][] = $contact;
            }
        }
        
        return $elementForWebService;
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param string $uri
     * @return mixed l'objet s'il existe, un message sinon
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $requestRes = $this->wsModel->getProjectByURI($sessionToken, $uri, $params);
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
}
