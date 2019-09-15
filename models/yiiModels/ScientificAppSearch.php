<?php

//******************************************************************************
//                         DataAnalysisAppSearch.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 25 Feb, 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSConstants;

include_once '../config/web_services.php';
require_once '../config/config.php';

/**
 * DataAnalysisAppSearch search class which makes link
 * between OpenSILEX and OpenCPU apps
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class ScientificAppSearch extends YiiScientificAppModel {

    /**
     * List all available R applications
     * This function creates all the necessary links to
     * include R application in OpenSILEX web application 
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
//        var_dump($params);exit;
        if (isset($params[YiiModelsConstants::PAGE])) {
            $this->page = $params[YiiModelsConstants::PAGE] - 1;
        }

        //2. Check validity of search data
        if (!$this->validate()) {
            return new \yii\data\ArrayDataProvider();
        }

        //3. Request to the web service and return result
        $findResult = $this->find($sessionToken, $this->attributesToArray(), "/applications");

        if (is_string($findResult)) {
            return $findResult;
        } else if (isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) && $findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN_INVALID) {
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

    public function shinyProxyServerStatus($sessionToken) {
        $requestRes = $this->find($sessionToken, [], "/shinyServerStatus");
        if (isset($requestRes->{WSConstants::METADATA}->{WSConstants::STATUS})) {
            $exception = $requestRes->{WSConstants::METADATA}->{WSConstants::STATUS}[0]->{WSConstants::EXCEPTION};
            return $exception->{"type"};
        }
        return null;
    }

    protected function arrayToAttributes($array) {
        
    }

}
