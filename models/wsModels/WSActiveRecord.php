<?php
//******************************************************************************
//                         WSActiveRecord.php
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: Feb, 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\wsModels;

/**
 * An active record for the web services. 
 * An adapted Active Record based to request web services
 * Based on the Yii Active Record of relational databases
 * See Yii2 ActiveRecord documentation for more details
 * @see http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 * @update [Arnaud Charleroy] 14 September, 2018: Fix totalCount attribute when only 
 * one element is returned 
 * @update [Morgane Vidal] 6 November, 2018: Add the management of the page number 
 * and the page size to send to the web service.
 * @update [Morgane Vidal] 19 March, 2019: Add generic function to transform a JSON
 * object into an array
 * @author Morgane Vidal <morgane.vidal@inra.fr>
 */
abstract class WSActiveRecord extends \yii\base\Model {
    
    //SILEX:todo
    //use trait representing wsModel instead of attribute wsModel ? 
    //\SILEX:todo
    /**
     * the web service connection model used for the service
     * @var WSModel
     */
    protected $wsModel;
    
    /**
     * the number of results to print per page
     * @var string
     */
    public $pageSize;
    
    /**
     * the number of the page wanted
     * @var string
     */
    public $page;
    
    /**
     * the total number of pages (given by the web service)
     * @var string
     */
    public $totalPages;
    
    /**
     * the total number of data (given by the web service)
     * @var string
     */
    public $totalCount;
    
    /**
     * true if the element is a new record, else false
     * @var boolean 
     */
    public $isNewRecord;
    
    /**
     * @param string $sessionToken the user session token
     * @param Array $attributes list of objects to send to the web service to 
     *                          be recorded. It is a key => value array. The key
     *                          is the name of the field. The array can be 
     *                          nested
     * @return mixed the messaged returned by the web service,
     *               "token" if the user needs to log in (invalid token).
     */
    public function insert($sessionToken, $attributes) {
        $requestRes =  $this->wsModel->post($sessionToken, "", $attributes);
        
        if (isset($requestRes->{WSConstants::TOKEN})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }
    
    /**
     * @param string $sessionToken the user session token
     * @param array $attributes list of objects to send to the web service to 
     *                          be recorded. It is a key => value array. The key
     *                          is the name of the field. The array can be 
     *                          nested
     * @return mixed the messaged returned by the web service,
     *               "token" if the user needs to log in (invalid token).
     */
    public function update($sessionToken, $attributes) {
        $requestRes = $this->wsModel->put($sessionToken, "", $attributes);
        if (isset($requestRes->{WSConstants::TOKEN})) {
            return WEB_SERVICE_TOKEN;
        } else {
            return $requestRes;
        }
    }
    
    /**
     * @param string $sessionToken the user session token
     * @param array $attributes the search params. It is a key => value array. The key
     *                          is the name of the field.
     * @return an array with the results,
               "token" if the user needs to log in (invalid token).
     */
    public function find($sessionToken, $attributes) {        
        $requestRes = $this->wsModel->get($sessionToken, "", $attributes);

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
     * Creates an array representing search parameters about pagination
     * Used for the web service for example
     * @return array with the attributes. 
     */
    public function attributesToArray() {
        $toReturn = null;
        $toReturn[WSConstants::PAGE] = $this->page;
        $toReturn[WSConstants::PAGE_SIZE] = $this->pageSize;
        
        return $toReturn;
    }
    
    /**
     * Allows to fill the attributes with the information in the array given 
     * @param array $array array key => value which contains the metadata of an image
     */
    abstract protected function arrayToAttributes($array);
    

    /**
     * Transforms a JSON object into an array
     * @param json jsonList
     * @return array
     */
    protected static function jsonListOfArraysToArray($jsonList) {
        $toReturn = [];
        if ($jsonList !== null) {
            foreach ($jsonList as $value) {
                $toReturn[] = $value;
            }
        }
        return $toReturn;
    }
}
