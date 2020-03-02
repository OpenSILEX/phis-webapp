<?php

//**********************************************************************************************
//                                       ScientificObjectSearch.php 
//
// Author(s): Morgane VIDAL
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2017
// Creation date: October 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  October, 3 2017
// Subject: ScientificObjectSearch represents the model behind the search form about app\models\YiiScientificObjectModel
//          Based on the Yii2 Search basic class
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\yiiModels\YiiScientificObjectModel;

/**
 * implements the search action for the scientific objects
 *
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class ScientificObjectSearch extends YiiScientificObjectModel {
    //SILEX:refactor
    //create a trait (?) with methods search and jsonListOfArray and use it in 
    //each class ElementNameSearch
    //\SILEX:refactor
    private $withProperties;
    const WITH_PROPERTIES = "withProperties";
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uri', 'label', 'experiment', 'alias', 'type', 'withProperties'], 'safe'],
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
        } else if (isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
                    && $findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN_INVALID) {
            return \app\models\wsModels\WSConstants::TOKEN_INVALID;
        } else {
            $resultSet = $this->jsonListOfArraysToArray($findResult);
            return new \yii\data\ArrayDataProvider([
                'models' => $resultSet,
                'pagination' => [
                    'pageSize' => $this->pageSize,
                    'totalCount' => $this->totalCount
                ],
                //SILEX:info
                //totalCount must be there too to get the pagination in GridView
                'totalCount' => $this->totalCount
                //\SILEX:info
            ]);
        }
    }
    
    function getWithProperties() {
        return $this->withProperties;
    }

    function setWithProperties($withProperties) {
        if(is_bool($withProperties)){
            $this->withProperties = $withProperties;
        }else{
            $this->withProperties = true;
        }
    }
    
    /**
     * Add with properties parameters
     * @return array
     */
    public function attributesToArray() {
        $elementForWebService = parent::attributesToArray();
        $elementForWebService[ScientificObjectSearch::WITH_PROPERTIES] = $this->getWithProperties(); 
        
        return $elementForWebService;
    }
}