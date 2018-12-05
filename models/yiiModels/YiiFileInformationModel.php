<?php

//******************************************************************************
//                                       YiiFileInformationModel.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 3 janv. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  3 janv. 2018
// Subject: the Yii model for the file informations
//******************************************************************************

namespace app\models\yiiModels;

/**
 * the model for file informations
 *
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class YiiFileInformationModel extends \app\models\wsModels\WSActiveRecord {
    
    /**
     * the extension of the file. 
     *  (e.g JPG)
     * @var string
     */
    public $extension;
    const EXTENSION = "extension";
    /**
     * the checksum (MD5) of the file
     *  (e.g 5317e65e7d7e9ee4c6f73a0744e6199f)
     * @var string 
     */
    public $checksum;
    const CHECKSUM = "checksum";
    /**
     * the server file path
     *  (e.g http://localhost/images/platform/2017/i170000000000.JPG)
     * @var string
     */
    public $serverFilePath;
    const SERVER_FILE_PATH = "serverFilePath";
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
            [['extension', 'checksum', 'serverFilePath'], 'safe']
        ];
    }
    
    /**
     * 
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return [
            'extension' => Yii::t('app', 'File Extension'),
            'checksum' => Yii::t('app', 'Checksum'),
            'serverFilePath' => Yii::t('app', 'Server File Path')
        ];
    }
    
    /**
     * allows to fill the attributes with the informations in the array given 
     * @param array $array array key => value which contains the concerned item
     */
    protected function arrayToAttributes($array) {
        $this->extension = $array[YiiFileInformationModel::EXTENSION];
        $this->checksum = $array[YiiFileInformationModel::CHECKSUM];
        $this->serverFilePath = $array[YiiFileInformationModel::SERVER_FILE_PATH];
    }

    /**
     * Create an array representing the file informations
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $toReturn = parent::attributesToArray();
        $toReturn[YiiFileInformationModel::EXTENSION]= $this->extension;
        $toReturn[YiiFileInformationModel::CHECKSUM] = $this->checksum;
        $toReturn[YiiFileInformationModel::SERVER_FILE_PATH] = $this->serverFilePath;
        
        return $toReturn;
    }
}
