<?php
//**********************************************************************************************
//                                       UserSearch.php 
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: Apr, 2017
// Contact: morgane.vidal@inra.fr, arnaud.charleroy@inra.fr, anne.tireau@inra.fr,
//          pascal.neveu@inra.fr
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\yiiModels\YiiUserModel;
use app\models\wsModels\WSConstants;

/**
 * UserSearch represents the model used for the search form about app\models\User
 * Based on the Yii2 Search basic classes
 * @author Morgane Vidal <morgane.vidal@inra.fr>, Arnaud Charleroy <arnaud.charleroy@inra.fr>
 * @update [Arnaud Charleroy] 19 September, 2018 : Pagination fixed
 */
class UserSearch extends YiiUserModel {
    //SILEX:refactor
    //create a trait (?) with methods search and jsonListOfArray and use it in 
    //each class ElementNameSearch
    //\SILEX:refactor
    
    public function __construct($pageSize = null, $page = null) {
        parent::__construct($pageSize, $page);
    }
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
          [['email', 'familyName', 'firstName', 'phone', 'affiliation', 'orcid', 'available', 'isAdmin', 'uri'], 'safe']  
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
}
