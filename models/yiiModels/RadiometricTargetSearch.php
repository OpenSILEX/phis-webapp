<?php

//******************************************************************************
//                                       RadiometricTargetSearch.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 27 September, 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

/**
 * Implements the search action for the radiometric target
 *
 * @author Migot Vincent <vincent.migot@inra.fr>
 */
class RadiometricTargetSearch extends YiiRadiometricTargetModel {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uri', 'label'], 'safe'],
        ];
    }

    /**
     * Search Radiometric target
     * 
     * @param array $sessionToken used for the data access
     * @param string $params search params
     * @return mixed DataProvider of the result 
     *               or string \app\models\wsModels\WSConstants::TOKEN if the user needs to log in
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
        } else if (isset($findResult[\app\models\wsModels\WSConstants::TOKEN])) {
            return $findResult;
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

    /**
     * transform the json into array
     * 
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
