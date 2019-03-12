<?php

//******************************************************************************
//                              DataFileSearch.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 3 jan. 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSConstants;
use yii\data\ArrayDataProvider;

/**
 * Implements the search action for the data files
 * @author Vincent Migot
 */
class DataFileSearch extends YiiDataFileModel {
    
    /**
     * @param string $pageSize number of elements per page
     *                               (limited to 150 000)
     * @param string $page number of the current page 
     */
    public function __construct($pageSize = null, $page = null) {
        parent::__construct($pageSize, $page);
    }
    
    /**
     * 
     * @return array the rules of the attributes
     */
    public function rules() {
        return [
          [['rdfType'], 'required'],
          [['concernedItems'], 'safe']  
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
         //1. this load the searched data 
        $this->load($params);
        if (isset($params[YiiModelsConstants::PAGE])) {
            $this->page = $params[YiiModelsConstants::PAGE];
        }
        
        //2. Check validity of search data
        if (!$this->validate()) {
            return new ArrayDataProvider();
        }

        //3. Request to the web service and return result
        $params = $this->attributesToArray();
        unset($params['uri']);
        $findResult = $this->find($sessionToken, $params);
        
        if (is_string($findResult)) {
            return $findResult;
        } else if (isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
                    && $findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === WSConstants::TOKEN) {
            return WSConstants::TOKEN;
        } else {
            return new ArrayDataProvider([
                'models' => $findResult,
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
