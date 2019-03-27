<?php

//******************************************************************************
//                                       SensorSearch.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 29 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  29 mars 2018
// Subject: SensorSearch represents the model behind the search form about
//          \app\models\Sensor based ont he Yii2 search basic classes
//******************************************************************************

namespace app\models\yiiModels;

use app\models\yiiModels\YiiSensorModel;

/**
 * implements the search action for the sensors
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class SensorSearch extends YiiSensorModel {
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand', 'label', 'inServiceDate', 'dateOfPurchase', 'dateOfLastCalibration'], 'safe'],
        ];
    }
    
    /**
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
        }  else if (isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
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
}
