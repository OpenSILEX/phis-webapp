<?php

//**********************************************************************************************
//                                       YiiVocabularyModel.php 
//
// Author(s): Arnaud Charleroy
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 13 july 2018
// Contact: arnaud.charleroy@.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  13 july, 2018
// Subject: The Yii model for the Vocabulary. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSActiveRecord;
use app\models\wsModels\WSVocabularyModel;
use Yii;

/**
 * The yii model for the vocabulary. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSAnnotationModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiVocabularyModel extends WSActiveRecord {

    public $label = "Vocabulary";

    /**
     * namespaces uses and their prefix
     *  (e.g.  [ oa : http://www.w3.org/ns/oa# ])
     * @var string
     */
    public $namespaces = [];

    const NAMESPACES = "namespaces";
    const NAMESPACES_LABEL = "Namespaces";

    public function __construct($pageSize = null, $page = null) {
        $this->wsModel = new WSVocabularyModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }

    public function rules() {
        return [
            [[YiiVocabularyModel::NAMESPACES], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            YiiVocabularyModel::NAMESPACES => Yii::t('app', YiiVocabularyModel::NAMESPACES_LABEL),
        ];
    }

    protected function arrayToNamespaces($array) {
        foreach ($array as $namespace) {
            $this->namespaces[$namespace->prefix] = $namespace->namespace;
        }
    }

    /**
     * Return namespaces list
     * @param string $sessionToken
     * @param string $uri
     * @return mixed the searched object if it exists or a message if not
     */
    public function getNamespaces($sessionToken) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }

        $requestRes = $this->wsModel->getNamespaces($sessionToken, $params);
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->arrayToNamespaces($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }

    protected function arrayToAttributes($array) {
        throw new \Exception('Not implemented');
    }

    public function attributesToArray(): array {
        throw new \Exception('Not implemented');
    }

}
