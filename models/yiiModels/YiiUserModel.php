<?php
//******************************************************************************
//                          YiiUserModel.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: Apr, 2017
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSUserModel;
use Yii;

class YiiUserModel extends WSActiveRecord {

    /**
     * The Yii model for the users. Used with web services
     * @var string email
     * @var string password
     * @var string firstName
     * @var string familyName
     * @var string phone
     * @var string address
     * @var string affiliation (ex: INRA - UMR MISTEA)
     * @var string orcid
     * @var string available
     * @var string isAdmin
     * @var string groups La liste des groupes auxquels l'utilisateur appartient
     * @author Morgane Vidal <morgane.vidal@inra.fr>
     * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
     * @update [Arnaud Charleroy] 23 august, 2018 : Update coding style and add user uri feature
     */
    public $email;

    const EMAIL = "email";

    public $password;

    const PASSWORD = "password";

    public $firstName;

    const FIRST_NAME = "firstName";

    public $familyName;

    const FAMILY_NAME = "familyName";

    public $phone;

    const PHONE = "phone";

    public $address;

    const ADDRESS = "address";

    public $affiliation;

    const AFFILIATION = "affiliation";

    public $orcid;

    const ORCID = "orcid";

    public $available;

    const AVAILABLE = "available";

    public $isAdmin;

    const ADMIN = "admin";

    public $groups;

    const GROUPS = "groups";
    const GROUPS_URIS = "groupsUris";

    public $uri;

    const URI = "uri";

    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSUserModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }

    public function rules() {
        return [
            [['email', 'affiliation', 'available','firstName', 'familyName'], 'required'],
            [['address', 'password', 'phone', 'orcid', 'affiliation', 'uri'], 'string', 'max' => 255],
            ['email', 'email'],
            [['firstName', 'familyName'], 'string', 'max' => 50],
            [['isAdmin', 'available'], 'boolean']
        ];
    }

    public function attributeLabels() {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'firstName' => Yii::t('app', 'First Name'),
            'familyName' => Yii::t('app', 'Family Name'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
            'affiliation' => Yii::t('app', 'Affiliation'),
            'orcid' => 'ORCID',
            'available' => Yii::t('app', 'Availability'),
            'isAdmin' => Yii::t('app', 'Admin'),
            'uri' => Yii::t('app', 'URI'),
            'groups' => Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2])
        ];
    }

    /**
     * Permet de remplir les attributs en fonction des informations 
     * comprises dans le tableau passé en paramètre
     * @param array $array tableau clé => valeur contenant les valeurs des attributs de l'utilisateur
     */
    protected function arrayToAttributes($array) {
        $this->email = $array[YiiUserModel::EMAIL];
        $this->password = $array[YiiUserModel::PASSWORD];
        $this->firstName = $array[YiiUserModel::FIRST_NAME];
        $this->familyName = $array[YiiUserModel::FAMILY_NAME];
        $this->address = $array[YiiUserModel::ADDRESS];
        $this->phone = $array[YiiUserModel::PHONE];
        $this->affiliation = $array[YiiUserModel::AFFILIATION];
        $this->orcid = $array[YiiUserModel::ORCID];
        $this->isAdmin = $array[YiiUserModel::ADMIN];
        $this->available = $array[YiiUserModel::AVAILABLE];
        $this->uri = $array[YiiUserModel::URI];
        if ($array[YiiUserModel::ADMIN] === "t" || $array[YiiUserModel::ADMIN] === "true") {
            $this->isAdmin = 1;
        } else {
            $this->isAdmin = 0;
        }

        foreach ($array[YiiUserModel::GROUPS] as $group) {
            $userGroup[YiiGroupModel::URI] = $group->{YiiGroupModel::URI};
            $userGroup[YiiGroupModel::LEVEL] = $group->{YiiGroupModel::LEVEL};
            $userGroup[YiiGroupModel::NAME] = $group->{YiiGroupModel::NAME};
            $this->groups[] = $userGroup;
        }
    }

    /**
     * @return array contenant l'élément à enregistrer en base de données
     *         cette méthode est publique pour que l'utilisateur puisse choisir de l'utiliser 
     *         ou d'envoyer lui-même son propre tableau (dans le cas où il souhaite enregistrer plusieurs instances)
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[YiiUserModel::EMAIL] = $this->email;
        $elementForWebService[YiiUserModel::PASSWORD] = $this->password;
        $elementForWebService[YiiUserModel::FIRST_NAME] = $this->firstName;
        $elementForWebService[YiiUserModel::FAMILY_NAME] = $this->familyName;
        $elementForWebService[YiiUserModel::ADDRESS] = $this->address;
        $elementForWebService[YiiUserModel::PHONE] = $this->phone;
        $elementForWebService[YiiUserModel::AFFILIATION] = $this->affiliation;
        if ($this->orcid !== "") {
            $elementForWebService[YiiUserModel::ORCID] = $this->orcid;
        }
        $elementForWebService[YiiUserModel::ADMIN] = $this->isAdmin;
//        $elementForWebService["available"] = $this->available;
        if ($this->groups != null) {
            foreach ($this->groups as $groupUri) {
                $elementForWebService[YiiUserModel::GROUPS_URIS][] = $groupUri;
            }
        }

        return $elementForWebService;
    }
    
    /**
     * 
     * @param string $sessionToken
     * @param string $email
     * @return mixed l'objet s'il existe, un message sinon
     */
    public function findByEmail($sessionToken, $email) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }

        $requestRes = $this->wsModel->getUserByEmail($sessionToken, $email, $params);
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

     /**
     * Return user searched by uri
     * @param string $sessionToken
     * @param string $uri
     * @return mixed the searched object if it exists or a message if not
     */
    public function findByUri($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }

        $requestRes = $this->wsModel->getUserByEmail($sessionToken, $uri, $params);
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
    
    /**
     * Get all the persons emails
     * @return array the list of the users mails and names existing in the database
     * @example returned array : 
     * [
     *      ["email@email.fr"] => "E Mail",
     *      ...
     * ]
     */
    public function getPersonsMailsAndName($sessionToken) {
        $users = $this->find($sessionToken, $this->attributesToArray());
        $usersToReturn = [];
        
        if ($users !== null) {
            //1. get the emails
            foreach($users as $user) {
                $usersToReturn[$user->email] = $user->firstName . " " . $user->familyName;
            }
            
            //2. if there are other pages, get the other users
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextUsers = $this->getPersonsMailsAndName($sessionToken);
                
                $usersToReturn = array_merge($usersToReturn, $nextUsers);
            }
            
            return $usersToReturn;
        }
    }
    
    /**
     * Get all the persons uris
     * @return array the list of the users mails and names existing in the database
     * @example returned array : 
     * [
     *      ["email@email.fr"] => "E Mail",
     *      ...
     * ]
     */
    public function getPersonsURIAndName($sessionToken) {
        $users = $this->find($sessionToken, $this->attributesToArray());
        $usersToReturn = [];
        
        if ($users !== null) {
            //1. get the emails
            foreach($users as $user) {
                if ($user->uri != null) {
                    $usersToReturn[$user->uri] = $user->firstName . " " . $user->familyName;
                }
            }
            
            //2. if there are other pages, get the other users
            if ($this->totalPages > $this->page) {
                $this->page++; //next page
                $nextUsers = $this->getPersonsURIAndName($sessionToken);
                
                $usersToReturn = array_merge($usersToReturn, $nextUsers);
            }
            
            return $usersToReturn;
        }
    }
}
