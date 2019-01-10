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

use app\models\yiiModels\YiiEventModel;

/**
 * implements the search action for the events
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventSearch extends YiiEventModel {
    
    public $concernsLabel;
    const CONCERNS_LABEL = 'concernsLabel';
    public $dateRange;
    const DATE_RANGE = 'dateRange';
    public $dateRangeStart;
    const DATE_RANGE_START = 'dateRangeStart';
    public $dateRangeEnd;
    const DATE_RANGE_END = 'dateRangeEnd';
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [[
            [
                YiiEventModel::TYPE,
                EventSearch::CONCERNS_LABEL,
                EventSearch::DATE_RANGE,
                EventSearch::DATE_RANGE_START,
                EventSearch::DATE_RANGE_END
            ],  'safe']]; 
    }
    
    /**
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
        }  else if (
            isset($findResult->{'metadata'}->{'status'}[0]->{'exception'}
                    ->{'details'}) 
            && $findResult->{'metadata'}->{'status'}[0]->{'exception'}
                    ->{'details'} === \app\models\wsModels\WSConstants::TOKEN
            ) {
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
                //totalCount must be there too to get the pagination in 
                //GridView
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
                    EventSearch::CONCERNS_LABEL => Yii::t('app', 'Concerned Elements'),
                    EventSearch::DATE_RANGE => Yii::t('app', 'Date')
                ]
        );
    }
    
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
            
        if (is_array($values)) {
            if (isset($values[EventSearch::DATE_RANGE])){
                $date_range = $values[EventSearch::DATE_RANGE];
                if (!empty($date_range))
                {
                    $dateRangeArray = explode(" - ", $date_range);
                    $this->dateRangeStart = $dateRangeArray[0];
                    
                    if (isset($dateRangeArray[1])){
                        $this->dateRangeEnd = $dateRangeArray[1];
                    }
                }
            }
        }
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
            EventSearch::CONCERNS_LABEL => $this->concernsLabel,
            EventSearch::DATE_RANGE_START => $this->dateRangeStart,
            EventSearch::DATE_RANGE_END => $this->dateRangeEnd
        ];
    }
}
