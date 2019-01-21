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

use Yii;
use DateTime;

use app\models\wsModels\WSConstants;
use app\models\yiiModels\YiiEventModel;

/**
 * Search action for the events
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventSearch extends YiiEventModel {
    
    /**
     * Searched concerned item's label 
     *  (e.g. "Plot 445")
     * @var string
     */
    public $concernedItemLabel;
    const CONCERNED_ITEM_LABEL = 'concernedItemLabel';
    
    /**
     * Searched date range 
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
     * Search Events
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
        }  else if (isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
            && $findResult->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === WSConstants::TOKEN) {
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
     * @see http://www.yiiframework.com/doc-2.0/guide-structure-models.html#attribute-labels
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
                parent::attributeLabels(),
                [
                    EventSearch::CONCERNED_ITEM_LABEL => Yii::t('app', 'Concerned Elements'),
                    EventSearch::DATE_RANGE => Yii::t('app', 'Date')
                ]
        );
    }
    
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
            
        if (is_array($values)) {
            if (isset($values[EventSearch::DATE_RANGE])) {
                $dateRange = $values[EventSearch::DATE_RANGE];
                if (!empty($dateRange)) {
                    $this->validateSubmittedDateRangeAndSetDatesAttributes($dateRange);
                }
            }
        }
    }
    
    private function validateSubmittedDateRangeAndSetDatesAttributes($submittedDateRangeString) {        
        $dateRangeArray = explode(Yii::$app->params['dateRangeSeparator'], $submittedDateRangeString);
        
        $isSubmittedStartDateFormatValid = true;
        $isSubmittedEndDateFormatValid = true;

        $submittedDateRangeStartString = $dateRangeArray[0];
        if (!empty($submittedDateRangeStartString)) {
            $isSubmittedStartDateFormatValid = $this->validateSubmittedDateFormat($submittedDateRangeStartString);
            if ($isSubmittedStartDateFormatValid) {
                $this->dateRangeStart = $submittedDateRangeStartString;
                if (isset($dateRangeArray[1])) {   
                    $submittedDateRangeEndString = $dateRangeArray[1];
                    $isSubmittedEndDateFormatValid = $this->validateSubmittedDateFormat($submittedDateRangeEndString);
                    if ($isSubmittedEndDateFormatValid) {
                        $this->dateRangeEnd = $submittedDateRangeEndString;
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
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        //return parent::attributesToArray();
        return [
            YiiEventModel::TYPE => $this->type,
            EventSearch::CONCERNED_ITEM_LABEL => $this->concernedItemLabel,
            EventSearch::DATE_RANGE_START => $this->dateRangeStart,
            EventSearch::DATE_RANGE_END => $this->dateRangeEnd
        ];
    }
}
