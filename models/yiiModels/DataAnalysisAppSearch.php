<?php

//******************************************************************************
//                         DataAnalysisAppSearch.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 25 Feb, 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use app\models\wsModels\WSConstants;
use app\models\wsModels\WSDataAnalysisModel;
use app\models\wsModels\WSActiveRecord;
include_once '../config/web_services.php';
require_once '../config/config.php';

/**
 * DataAnalysisAppSearch search class which makes link
 * between OpenSILEX and OpenCPU apps
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisAppSearch extends WSActiveRecord{

    /**
     * List all available R applications
     * This function creates all the necessary links to
     * include R application in OpenSILEX web application 
     * 
     * @param array $sessionToken used for the data access
     * @param array $params search params (maybe used to filter apps)
     * @return array list of app with their informations
     */
    public function search($params = null) {
        $model =  new WSDataAnalysisModel();
          //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;   
        $searchResult = $model->getApplications(Yii::$app->session[WSConstants::ACCESS_TOKEN], $params);

        if (is_string($searchResult)) {
            return $searchResult;
        }  else if (isset($searchResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
                    && $searchResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN) {
            return \app\models\wsModels\WSConstants::TOKEN;
        } else {
            $resultSet = $this->jsonListOfArraysToArray($searchResult);
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

    protected function arrayToAttributes($array) {
        
    }

}
