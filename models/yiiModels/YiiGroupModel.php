<?php

//**********************************************************************************************
//                                       YiiGroupModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: April 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  April, 2017
// Subject: The Yii model for the Groups. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSGroupModel;

use Yii;

/**
 * The yii model for the groups. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSGroupModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiGroupModel extends WSActiveRecord {
    
    /**
     * the group's uri
     *  (e.g http://phenome-fppn.fr/diaphen/INRA-MISTEA-GAMMA)
     * @var string
     */
    public $uri;
    const URI = "uri";
    /**
     * the group's name
     *  (e.g. GAMMA)
     * @var string
     */
    public $name;
    const NAME = "name";
    /**
     * the group's level
     * owner or guest
     * owner can read and update data
     * guest can read data
     *  (e.g owner)
     * @var string
     */
    public $level;
    const LEVEL = "level";
    /**
     * the group's description
     * @var string
     */
    public $description;
    const DESCRIPTION = "description";
    /**
     * the group's organism 
     *  (e.g. INRA-MISTEA)
     * @var string
     */
    public $organism;
    /**
     * the group's members (email)
     *  (e.g. john.doe[at]email.fr)
     * @var array<string>
     */
    public $users;
    const USERS = "users";
    const USERS_EMAILS = "usersEmails";
     
    /**
     * Initialize wsModel. In this class, wsModel is a WSGroupModel
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSGroupModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['uri', 'name', 'level', 'description', 'organism', 'laboratoryName'], 'required'],
          [['description'], 'string'], 
          [['users'], 'safe'],
          [['organism', 'laboratoryName'], 'string', 'max' => 50],
          [['name', 'uri', 'level'], 'string', 'max' => 200]
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
          'name' => Yii::t('app', 'Name'),
          'uri' => Yii::t('app', 'URI'),
          'level' => Yii::t('app', 'Level'),
          'description' => Yii::t('app', 'Description'),
          'organism' => Yii::t('app', 'Organism'),
          'laboratoryName' => Yii::t('app', 'laboratoryName'),
          'users' => Yii::t('app', 'Members')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the metadata of 
     *                     a group
     */
    protected function arrayToAttributes($array) {
        $this->name = $array[YiiGroupModel::NAME];
        $this->level = $array[YiiGroupModel::LEVEL];
        $this->uri = $array[YiiGroupModel::URI];
        $this->description = $array[YiiGroupModel::DESCRIPTION];
        
        if (isset($array[YiiGroupModel::USERS])) {
            foreach ($array[YiiGroupModel::USERS] as $user) {
                $u[YiiUserModel::EMAIL] = $user->{YiiUserModel::EMAIL};
                $u[YiiUserModel::FIRST_NAME] = $user->{YiiUserModel::FIRST_NAME};
                $u[YiiUserModel::FAMILY_NAME] = $user->{YiiUserModel::FAMILY_NAME};
                $this->users[] = $u;
            }
        }
    }

    /**
     * Create an array representing the group
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        if (isset($this->organism) && $this->organism != null) {
            $elementForWebService[YiiGroupModel::NAME] = $this->organism . "-" . $this->name;
        } else {
            $elementForWebService[YiiGroupModel::NAME] = $this->name;
        }
        $elementForWebService[YiiGroupModel::LEVEL] = $this->level;
        $elementForWebService[YiiGroupModel::URI] = $this->uri;
        $elementForWebService[YiiGroupModel::DESCRIPTION] = $this->description;
        if ($this->users != null) {
            foreach ($this->users as $userEmail) {
                $elementForWebService[YiiGroupModel::USERS_EMAILS][] = $userEmail;
            }
        }
        
        return $elementForWebService;
    }
    
    /**
     * get group's informations by uri
     * @param string $sessionToken user session token
     * @param string $name group's uri
     */
    public function findByName($sessionToken, $name) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }
        
        $requestRes = $this->wsModel->getGroupByName($sessionToken, $name, $params);
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN_INVALID])) {
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
