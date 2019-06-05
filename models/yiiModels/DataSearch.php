<?php

//******************************************************************************
//                                       DataSearch.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 22 mai 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use \app\models\wsModels\WSConstants;
use Yii;
use DateTime;

/**
 * implements the search action for the data
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
class DataSearch extends YiiDataModel {
    /**
     * Start date of the search data.
     * @var string expected format : YYYY-MM-DDTHH:MM:SSZ
     */
    public $startDate;
    /**
     * End date of the search data.
     * @var string expected format : YYYY-MM-DDTHH:MM:SSZ
     */
    public $endDate;
    /**
     * Label of the object concerned by the searched data.
     * @var string
     */
    public $objectLabel;
    /**
     * Label of the provenance of the data.
     * @var string
     */
    public $provenanceLabel;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variable', 'startDate', 'endDate', 'object', 'objectLabel', 'provenance', 'provenanceLabel'], 'safe'],
        ];
    }
    
    /**
     * 
     * @param type $sessionToken
     * @param type $variableUri
     * @param type $startDate
     * @param type $endDate
     * @param type $objectUri
     * @param type $objectLabel
     * @param type $provenanceUri
     * @param type $provenanceLabel
     * @param type $dateSortAsc
     */
    public function searchData($sessionToken) {
        $params = [
          WSConstants::PAGE => $this->page,
          WSConstants::PAGE_SIZE => $this->pageSize,
          "variableUri" => $this->variable,
          "startDate" => $this->startDate,
          "endDate" => $this->endDate,
          "objectLabel" => $this->object,
          "provenanceLabel" => $this->provenance,
          "dateSortAsc" => null
        ];
        
        $requestRes = $this->wsModel->getDataSearch($sessionToken, $params);
        
        if (isset($requestRes->{WSConstants::METADATA}->{WSConstants::PAGINATION})) {
            $this->totalPages = $requestRes->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_PAGES};
            $this->totalCount = $requestRes->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::TOTAL_COUNT};
            $this->page = $requestRes->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::CURRENT_PAGE};
            $this->pageSize = $requestRes->{WSConstants::METADATA}->{WSConstants::PAGINATION}->{WSConstants::PAGE_SIZE};
        } else {
            //SILEX:info
            // A null pagination means only one result
            //\SILEX:info
            $this->totalCount = 1;
        }
        
        if (isset($requestRes->{WSConstants::RESULT}->{WSConstants::DATA}))  {
            return (array)$requestRes->{WSConstants::RESULT}->{WSConstants::DATA};
            
        } else {
            return $requestRes;
        }
    }
    
    /**
     * Search data, using the searchData method of the YiiDataModel
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
        $findResult = $this->searchData($sessionToken);
        
        if (is_string($findResult)) {
            return $findResult;
        }  else if (isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
                    && $findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN) {
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
                //totalCount must be there too to get the pagination in GridView
                'totalCount' => $this->totalCount
                //\SILEX:info
            ]);
        }
    }
    
    /**
     * @inheritdoc
     * @param type $values
     * @param type $safeOnly
     */
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
           
        if (is_array($values)) {
            if (isset($values['date'])) {
                $dateRange = $values['date'];
                
                //SILEX:info
                // We shouldn't control the date range format because the WS 
                // already implements it but as the webapp doesn't handle WS
                // error responses yet, we have to control the format of the 
                // submitted date range at the moment.
                //\SILEX:info
                if (!empty($dateRange)) {
                    $this->validateDateRangeFormatAndSetDatesAttributes($dateRange);
                }
            }
        }
    }
    
    /**
     * Validates the date range format and set the dates attributes. The accepted 
     * date range format is defined in the application parameter 
     * standardDateTimeFormatPhp.
     * @param type $dateRangeString
     */
    private function validateDateRangeFormatAndSetDatesAttributes($dateRangeString) {        
        $dateRangeArray = explode(Yii::$app->params['dateRangeSeparator'], $dateRangeString);

        // validate start date
        $submittedStartDateString = $dateRangeArray[0];
        if (!empty($submittedStartDateString)) {
            $this->startDate = $submittedStartDateString;
            if (isset($dateRangeArray[1])) {   
                $submittedEndDateString = $dateRangeArray[1];
                $this->endDate = $submittedEndDateString;
            }
        }
    }
}