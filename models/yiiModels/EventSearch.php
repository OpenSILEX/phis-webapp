<?php

//******************************************************************************
//                                       EventSearch.php
//
// Author(s): Andréas Garcia <andreas.garcia@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation dateTimeString: 02 janvier 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\yiiModels\YiiEventModel;

/**
 * implements the search action for the events
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventSearch extends YiiEventModel {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [ 
            [['uri', 'type', 'concernsItems', 'dateTimeString', 'documents'
                ,'properties'], 'safe']
        ]; 
    }
    
    /**
     * @param array $sessionToken
     * @param string $params
     * @return mixed DataProvider of the result or string 
     * \app\models\wsModels\WSConstants::TOKEN if the user needs to log in
     */
    public function search($sessionToken, $params) {
        $this->load($params);
        if (isset($params[YiiModelsConstants::PAGE])) {
            $this->page = $params[YiiModelsConstants::PAGE];
        }
        
        if (!$this->validate()) {
            return new \yii\data\ArrayDataProvider();
        }
        
        //Request to the web service and return result
        $findResult = $this->find($sessionToken, $this->attributesToArray());
        
        if (is_string($findResult)) {
            return $findResult;
        }  else if (
            isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}
                    ->{'details'}) 
            && $findResult->{'metadata'}->{'status'}[0]->{'exception'}
                    ->{'details'} === \app\models\wsModels\WSConstants::TOKEN
            ) {
            return \app\models\wsModels\WSConstants::TOKEN;
        } else {
            $resultSet = $this->jsonListOfArraysToArray($findResult);
            return new \yii\data\ArrayDataProvider([
                'models' => $resultSet,
                'pagination' => [
                    'pageSize' => $this->pageSize,
                    'totalCount' => $this->totalCount
                ],
                //SILEX:info
                //totalCount must be there too to get the pagination in 
                //GridView
                'totalCount' => $this->totalCount
                //\SILEX:info
            ]);
        }
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
