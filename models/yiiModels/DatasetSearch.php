<?php

//**********************************************************************************************
//                                       DatasetSearch.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October, 24 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 24 2017
// Subject: DatasetSearch represents the model behind the search form about app\models\YiiDatasetModel
//          Based on the Yii2 Search basic class
//***********************************************************************************************

namespace app\models\yiiModels;
use app\models\yiiModels\YiiDatasetModel;

/**
 * implements the search action for the dataset
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DatasetSearch extends YiiDatasetModel {
    
    //SILEX:refactor
    //create a trait (?) with methods search and jsonListOfArray and use it in 
    //each class ElementNameSearch
    //\SILEX:refactor
    
    /**
     * start date of the searched data
     * @var string
     */
    public $dateStart;
    /**
     * end date of the searched data
     * @var string 
     */
    public $dateEnd;
    /**
     * experiement uri of the searched data
     * @var string
     */
    public $experimentURI;
    /**
     * agronomical objects uri of the searched data
     * @var array<string>
     */
    public $agronomicalObjects;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variables', 'dateStart', 'dateEnd', 'experimentURI', 'agronomicalObjects'], 'safe'],
            [['experimentURI', 'agronomicalObjects'], 'string']
        ];
    }
    
    /**
     * 
     * @param array $sessionToken used for the data access
     * @param string $params search params
     * @return mixed DataProvider of the result 
     *               or string "token" if the user needs to log in
     */
    public function search($sessionToken, $params) {
        //1. load the searched params 
        $this->load($params);
        if (isset($params[YiiModelsConstants::PAGE])) {
            $this->page = $params[YiiModelsConstants::PAGE];
        }
        
        //2. Check validity of search data
        if (!$this->validate()) {
            return new \yii\data\ArrayDataProvider();
        }
        
        //3. Request to the web service and return result
        $findResult = $this->find($sessionToken, $this->attributesToArray());
        
        if (is_string($findResult)) {
            return $findResult;
        } else if ($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN) {
            return \app\models\wsModels\WSConstants::TOKEN;
        } else {
            $resultSet = $this->jsonListOfArraysToArray($findResult);
            return new \yii\data\ArrayDataProvider([
                'models' => $resultSet,
                'pagination' => [
                    'pageSize' => $this->pageSize,
                    'totalCount' => $this->totalCount
                ],
                'totalCount' => $this->totalCount
            ]);
        }
    }
    
    /**
     * override YiiDatasetModel::attributesToArray().
     * @see YiiDatasetModel::attributesToArray()
     * @return array data to send to service
     */
    public function attributesToArray() {
        $toReturn["experiment"] = $this->experimentURI;
        $toReturn["variable"] = $this->variables;
        $toReturn["agronomicalObjects"] = $this->agronomicalObjects;
        $toReturn["startDate"] = $this->dateStart;
        $toReturn["endDate"] = $this->dateEnd;
                
        return $toReturn;
    }
    
    /**
     * transform the json into array
     * @param json jsonList
     * @return array
     */
    private function jsonListOfArraysToArray($jsonList) {
        $toReturn = []; 
        if ($jsonList !== null) {
            foreach ($jsonList as $value) {
                $toReturn[] = $value;
            }
        } 
        return $toReturn;
    }
}