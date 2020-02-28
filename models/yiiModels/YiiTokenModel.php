<?php

//**********************************************************************************************
//                                       YiiTokenModel.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  February, 2017
// Subject: Model corresponding to the session Tokens. Used to get the session token of the user
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSTokenModel;
use Yii;
use Lcobucci\JWT\Configuration;


class YiiTokenModel extends WSActiveRecord {
    
    /**
     *
     * @var string email
     * @var string password
     */
    public $email;
    const EMAIL = "email";
    public $password;
    const PASSWORD = "password";
    
    const IS_ADMIN = "isAdmin";
    const ACCESS_TOKEN = "access_token";
    const IS_GUEST = "isGuest";
    const URI = "uri";
    
    
    public function __construct() {
        $this->wsModel = new WSTokenModel();
    }
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            ['email', 'email']
        ];
    }
    
    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-structure-models.html#attribute-labels
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password')
        ];
    }

    
    /**
     * Effectue la connexion au web service : si les identifiants sont les bons,
     * affecte le token de session de connexion à la session.
     * /!\ dans le cas de mauvais logins, access_token sera égal à null
     * @param string $email
     * @param string $password
     * @return boolean true si le login c'est bien passé, false sinon
     */
    public function login() {
        $requestResult = $this->wsModel->getToken($this->email, $this->password);
        if ($requestResult != null) {
            Yii::$app->session[YiiTokenModel::ACCESS_TOKEN] = $requestResult;
            Yii::$app->session[YiiTokenModel::EMAIL] = $this->email;
            Yii::$app->session[YiiTokenModel::IS_GUEST] = false;
            $decoded = Configuration::forUnsecuredSigner()->getParser()->parse($requestResult);
            Yii::$app->session[YiiTokenModel::URI] = $decoded->claims()->get("sub");
            Yii::$app->session[YiiTokenModel::IS_ADMIN] = $decoded->claims()->get("is_admin", false);
            return true;
        } else {
            Yii::$app->session[YiiTokenModel::IS_GUEST] = true;
            Yii::$app->session[YiiTokenModel::ACCESS_TOKEN] = null;
            return false;
        }
    }

    protected function arrayToAttributes($array) {
        $this->email = $array[YiiTokenModel::EMAIL];
        $this->password = $array[YiiTokenModel::PASSWORD];
    }

    public function attributesToArray() {
        $tab[YiiTokenModel::EMAIL] = $this->email;
        $tab[YiiTokenModel::PASSWORD] = $this->password;
    }
}
