<?php
//******************************************************************************
//                               EventSearch.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 2 jan. 2019
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
 * @update [Bonnefont Julien] 1 octobre, 2019: return exception on search action
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 * 
 */
class EventSearch extends YiiEventModel {
    
    /**
     * Type filter.
     * @example MoveFrom
     * @var string
     */
    public $searchType;
    const SEARCH_TYPE = 'searchType';
    const SEARCH_TYPE_WS_FIELD = 'rdfType';
    
    /**
     * Concerned item's label filter.
     * @example Plot 445
     * @var string
     */
    public $searchConcernedItemLabel;
    const SEARCH_CONCERNED_ITEM_LABEL = 'searchConcernedItemLabel';
    const SEARCH_CONCERNED_ITEM_LABEL_WS_FIELD = 'concernedItemLabel';
    
    /**
     * Concerned item's URI filter.
     * @example Plot 445
     * @var string
     */
    public $searchConcernedItemUri;
    const SEARCH_CONCERNED_ITEM_URI = 'searchItemUri';
    const SEARCH_CONCERNED_ITEM_URI_WS_FIELD = 'concernedItemUri';
    
    /**
     * Date range filter.
     * @example 2019-01-02T00:00:00+01:00 - 2019-01-03T23:00:00+01:00
     * @var string
     */
    public $searchDateRange;
    const SEARCH_DATE_RANGE = 'searchDateRange';
    
    /**
     * Date range start filter.
     * @example 2019-01-02T00:00:00+01:00
     * @var string
     */
    public $searchDateRangeStart;
    const SEARCH_DATE_RANGE_START = 'searchStartDate';
    const SEARCH_DATE_RANGE_START_WS_FIELD = 'startDate';
    
    /**
     * Date range end filter.
     * @example 2019-01-02T00:00:00+01:00
     * @var string
     */
    public $searchDateRangeEnd;
    const SEARCH_DATE_RANGE_END = 'searchEndDate';
    const SEARCH_DATE_RANGE_END_WS_FIELD = 'endDate';
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [[
            [
                self::SEARCH_TYPE,
                self::SEARCH_CONCERNED_ITEM_LABEL,
                self::SEARCH_CONCERNED_ITEM_URI,
                self::SEARCH_DATE_RANGE,
                self::SEARCH_DATE_RANGE_START,
                self::SEARCH_DATE_RANGE_END
            ],  'safe']]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
            parent::attributeLabels(),
            [
                self::SEARCH_TYPE => Yii::t('app', self::TYPE_LABEL),
                self::SEARCH_CONCERNED_ITEM_LABEL => Yii::t('app', self::CONCERNED_ITEMS_LABEL),
                self::SEARCH_DATE_RANGE => Yii::t('app', self::DATE_LABEL)
            ]
        );
    }
    
    /**
     * @param array $sessionToken
     * @param string $searchParams
     * @return mixed DataProvider of the result or string 
     * \app\models\wsModels\WSConstants::TOKEN if the user needs to log in
     */
    public function search($sessionToken, $searchParams) {
        $this->load($searchParams);
        
        if (!$this->validate()) {
            return new ArrayDataProvider();
        }      
        return $this->getEventProvider($sessionToken, $searchParams);
    }
    
    /**
     * Requests to WS and returns result.
     * @param $sessionToken
     * @return request result
     */
    private function getEventProvider($sessionToken, $searchParams) {
        
        $results = $this->find($sessionToken, $this->attributesToArray());
        // var_dump($results);exit;
        if (is_string($results)) {
            return $results;
        }  else if (isset($results->{'metadata'}->{'status'}[0]->{'exception'}->{'details'}) 
                    && $results->{'metadata'}->{'status'}[0]->{'exception'}->{'details'} === \app\models\wsModels\WSConstants::TOKEN_INVALID) {
            return \app\models\wsModels\WSConstants::TOKEN_INVALID;
        } else if (isset($results->{'metadata'}->{'status'}[0]->{'exception'}->{'details'})) {
            return $results->{'metadata'}->{'status'}[0]->{'exception'}->{'details'};
        } else {
            $events = $this->jsonListOfArraysToArray($results);
            return new ArrayDataProvider([
                'models' => $events,
                'pagination' => [
                    'pageSize' => $searchParams[WSConstants::PAGE_SIZE],
                    'totalCount' => $this->totalCount
                ],
                'totalCount' => $this->totalCount
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
            if (isset($values[self::SEARCH_DATE_RANGE])) {
                $dateRange = $values[self::SEARCH_DATE_RANGE];
                
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
        
        $isSubmittedStartDateFormatValid = true;
        $isSubmittedEndDateFormatValid = true;

        // validate start date
        $submittedStartDateString = $dateRangeArray[0];
        if (!empty($submittedStartDateString)) {
            $isSubmittedStartDateFormatValid = $this->validateSubmittedDateFormat($submittedStartDateString);
            if ($isSubmittedStartDateFormatValid) {
                $this->searchDateRangeStart = $submittedStartDateString;
                // validate end date
                if (isset($dateRangeArray[1])) {   
                    $submittedEndDateString = $dateRangeArray[1];
                    $isSubmittedEndDateFormatValid = $this->validateSubmittedDateFormat($submittedEndDateString);
                    if ($isSubmittedEndDateFormatValid) {
                        $this->searchDateRangeEnd = $submittedEndDateString;
                    }
                } 
            }
        }
        
        if (!$isSubmittedStartDateFormatValid || !$isSubmittedEndDateFormatValid) {
            $this->resetDateRangeFilterValues();
        }
    }
    
    /**
     * Validates the submitted date format. The accepted date format is defined 
     * in the application parameter standardDateTimeFormatPhp
     * @param type $dateString
     * @return boolean
     */
    private function validateSubmittedDateFormat($dateString) {
        /* //SILEX:info
         * Steps to validate a date format of a date string:
         *  - create a DateTime object from this date string and its format
         *  - transform this DateTime into a string using this format
         *  if there is no parsing error and if the final date string and the 
         *  first one are equal, then the date format is valid
         * //\SILEX:info
         */
        try {
            /* //SILEX:info
             * the standard date format provide a 'T' between the date and the 
             * time but the PHP date format parser interprets the 'T' as the
             * timezone part of the date. (See http://php.net/manual/en/function.date.php)
             * So, before analysing that a date has
             * a valid date format, we have to replace the 'T' by a neutral char
             * (like a space) in order to be able to use the PHP parser 
             * thereafter.
             * //\SILEX:info
             */
            $dateStringWithoutT = str_replace("T", " ", $dateString);
            $date = DateTime::createFromFormat(Yii::$app->params['dateTimeFormatPhp'], $dateStringWithoutT);
            $dateRangeStartParseErrorCount = DateTime::getLastErrors()['error_count']; 
            if ($dateRangeStartParseErrorCount >= 1) {
                error_log("dateRangeStartParseErrorMessages ".print_r(DateTime::getLastErrors()['errors'], true)); 
                return false;
            }
            else if ($date->format(Yii::$app->params['dateTimeFormatPhp']) == $dateStringWithoutT) {
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
    
    /**
     * Resets the date range filter values
     */
    private function resetDateRangeFilterValues(){
        $this->searchDateRangeStart = null;
        $this->searchDateRangeEnd = null;
        $this->searchDateRange = null;
    }
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        return [
            YiiModelsConstants::PAGE => $this->page,
            YiiModelsConstants::PAGE_SIZE => $this->pageSize,
            self::SEARCH_TYPE_WS_FIELD => $this->rdfType,
            self::SEARCH_CONCERNED_ITEM_LABEL_WS_FIELD => $this->searchConcernedItemLabel,
            self::SEARCH_CONCERNED_ITEM_URI_WS_FIELD => $this->searchConcernedItemUri,
            self::SEARCH_DATE_RANGE_START_WS_FIELD => $this->searchDateRangeStart,
            self::SEARCH_DATE_RANGE_END_WS_FIELD => $this->searchDateRangeEnd
        ];
    }
}
