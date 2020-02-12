<?php

//******************************************************************************
//                                       DataSearch.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 12 mars 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

/**
 * implements the search action for the data
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DataSearchLayers extends \app\models\yiiModels\YiiDataModel {
    /**
     * Start date of the searched data
     * @var string
     */
    public $startDate;
    /**
     * End date of the searched data
     * @var string 
     */
    public $endDate;
    
    /**
     * Parameter to sort result by date.
     * @var string expected values: true or false
     */
    public $dateSortAsc;
    
    /**
     * @inheritdoc
     */
    
    public function rules()
    {
        return [
            [['variable', 'startDate', 'endDate', 'provenance', 'object','dateSortAsc'], 'safe']
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
                'totalCount' => $this->totalCount
            ]);
        }
    }
    
    /**
     * override YiiDataModel::attributesToArray().
     * @see YiiDataModel::attributesToArray()
     * @return array data to send to service
     */
    public function attributesToArray() {
        $toReturn["variable"] = $this->variable;
        $toReturn["object"] = $this->object;
        $toReturn["startDate"] = $this->startDate;
        $toReturn["endDate"] = $this->endDate;
        $toReturn["provenance"] = $this->provenance;
        $toReturn["pageSize"] = $this->pageSize;
        $toReturn["dateSortAsc"] = $this->dateSortAsc;
                
        return $toReturn;
    }
}