<?php

//******************************************************************************
//                               EventSearch.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 02 jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\models\yiiModels;

use Yii;
use DateTime;

use yii\data\ArrayDataProvider;
use app\models\wsModels\WSConstants;
use app\models\yiiModels\YiiEventModel;

/**
 * Search action for the events
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventSearch extends YiiEventModel {
    
    /**
     * Concerned item's label filter
     * @example Plot 445
     * @var string
     */
    public $concernedItemLabel;
    const CONCERNED_ITEM_LABEL = 'concernedItemLabel';
    
    /**
     * Date range filter
     * @example 2019-01-02T00:00:00+01:00 - 2019-01-03T23:00:00+01:00
     * @var string
     */
    public $dateRange;
    const DATE_RANGE = 'dateRange';
    public $dateRangeStart;
    const DATE_RANGE_START = 'startDate';
    public $dateRangeEnd;
    const DATE_RANGE_END = 'endDate';
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [[
            [
                YiiEventModel::TYPE,
                EventSearch::CONCERNED_ITEM_LABEL,
                EventSearch::DATE_RANGE,
                EventSearch::DATE_RANGE_START,
                EventSearch::DATE_RANGE_END
            ],  'safe']]; 
    }
    
    /**
     * @param array $sessionToken
     * @param string $searchParams
     * @return mixed DataProvider of the result or string 
     * \app\models\wsModels\WSConstants::TOKEN if the user needs to log in
     */
    public function search($sessionToken, $searchParams) {
        $this->load($searchParams);
        if (isset($searchParams[YiiModelsConstants::PAGE])) {
            $this->page = $searchParams[YiiModelsConstants::PAGE];
        }
        
        if (!$this->validate()) {
            return new ArrayDataProvider();
        }
        
        return $this->requestToWSAndReturnResult($sessionToken);
    }
    
    /**
     * Request to WS and return result
     * @param $sessionToken
     * @return request result
     */
    private function requestToWSAndReturnResult($sessionToken) {
        $results = $this->find($sessionToken, $this->attributesToArray());
        
        if (is_string($results)) {
            return $results;
        }  else if (isset($results->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
            && $results->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === WSConstants::TOKEN) {
            return WSConstants::TOKEN;
        } else {
            $resultSet = $this->jsonListOfArraysToArray($results);
            return new ArrayDataProvider([
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
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
                parent::attributeLabels(),
                [
                    EventSearch::CONCERNED_ITEM_LABEL => Yii::t('app', 'Concerned Items'),
                    EventSearch::DATE_RANGE => Yii::t('app', 'Date')
                ]
        );
    }
    
    /**
     * @inheritdoc
     * @param type $values
     * @param type $safeOnly
     */
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
            
        if (is_array($values)) {
            if (isset($values[EventSearch::DATE_RANGE])) {
                $dateRange = $values[EventSearch::DATE_RANGE];
                
                //SILEX:info
                // We shouldn't control the date range format because de WS 
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
     * 
     * @param type $dateRangeString
     */
    private function validateDateRangeFormatAndSetDatesAttributes($dateRangeString) {        
        $dateRangeArray = explode(Yii::$app->params['dateRangeSeparator'], $dateRangeString);
        
        $isSubmittedStartDateFormatValid = true;
        $isSubmittedEndDateFormatValid = true;

        $submittedStartDateString = $dateRangeArray[0];
        if (!empty($submittedStartDateString)) {
            $isSubmittedStartDateFormatValid = $this->validateSubmittedDateFormat($submittedStartDateString);
            if ($isSubmittedStartDateFormatValid) {
                $this->dateRangeStart = $submittedStartDateString;
                if (isset($dateRangeArray[1])) {   
                    $submittedEndDateString = $dateRangeArray[1];
                    $isSubmittedEndDateFormatValid = $this->validateSubmittedDateFormat($submittedEndDateString);
                    if ($isSubmittedEndDateFormatValid) {
                        $this->dateRangeEnd = $submittedEndDateString;
                    }
                } 
            }
        }
        
        if (!$isSubmittedStartDateFormatValid || !$isSubmittedEndDateFormatValid) {
            $this->handleSearchDateRangeFormatError();
        }
    }
    
    private function validateSubmittedDateFormat($dateString) {
        /*
         * Steps to validate a date format of a date string:
         *  - create a DateTime object from this date string and its format
         *  - transform this DateTime into a string using this format
         *  if there is no parsing error and if the final date string and the 
         * first one are equal, then the date format is valid
         */
        try {
            /* the standard date format provide a 'T' between the date and the 
             * time but the PHP date format parser interprets the 'T' as the
             * timezone part of the date. (See http://php.net/manual/en/function.date.php)
             * So, before analysing that a date has
             * a valid date format, we have to replace the 'T' by a neutral char
             * (like a space) in order to be able to use the PHP parser 
             * thereafter.
             */
            $dateStringWithoutT = str_replace("T", " ", $dateString);
            $date = DateTime::createFromFormat(Yii::$app->params['standardDateTimeFormatPhp'], $dateStringWithoutT);
            $dateRangeStartParseErrorCount = DateTime::getLastErrors()['error_count']; 
            if ($dateRangeStartParseErrorCount >= 1) {
                error_log("dateRangeStartParseErrorMessages ".print_r(DateTime::getLastErrors()['errors'], true)); 
                return false;
            }
            else if ($date->format(Yii::$app->params['standardDateTimeFormatPhp']) == $dateStringWithoutT) {
                return true;
            }
            else {
                return false;
            }
        } catch (Exception $exception) {                
            error_log($exception->getMessage());
            return false;
        }
    }
    
    private function handleSearchDateRangeFormatError(){
        $this->dateRangeStart = null;
        $this->dateRangeEnd = null;
        $this->dateRange = null;
    }
    
    /**
     * Transform the json into array
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
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        return [
            YiiEventModel::TYPE => $this->type,
            EventSearch::CONCERNED_ITEM_LABEL => $this->concernedItemLabel,
            EventSearch::DATE_RANGE_START => $this->dateRangeStart,
            EventSearch::DATE_RANGE_END => $this->dateRangeEnd
        ];
    }
}
