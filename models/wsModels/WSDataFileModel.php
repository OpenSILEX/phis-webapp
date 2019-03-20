<?php
//******************************************************************************
//                               WSDataFileModel.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 20 mars 2019
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\wsModels;

include_once '../config/web_services.php';

/**
 * Allows the acces to the images service
 * @author Vincent Migot <vincent.migot@inra.fr>
 */
class WSDataFileModel extends \openSILEX\guzzleClientPHP\WSModel {
    
    /**
     * initialize access to the images service. Calls super constructor
     */
    public function __construct() {
        parent::__construct(WS_PHIS_PATH, "data/file/search");
    }
    
    /**
     * Send a get request to the web service
     * Overide GuzzleHttp method to allow array query params
     * @param String $sessionToken the user session token
     * @param String $subService the "sub service" called. e.g. /{uri}
     * @param Array  $params key => value with the data to send to the get, in the url
     * e.g.
     * [
     *  "page" => "0",
     *  "pageSize" => "1000",
     *  "uri" => "http://uri/of/my/entity"
     * ]
     * @return string if error the error message
     *                else the json of the web service result
     */
    public function get($sessionToken, $subService, $params = null, $bodyToSend = null) {
        //Prepare the query with the body
        $requestParamsPath = "";
        $body = json_encode($bodyToSend, $options = JSON_UNESCAPED_SLASHES);
        
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $valueItem) {
                        ($requestParamsPath == "") ?
                            $requestParamsPath .= "?" . $key . "=" . urlencode($valueItem)
                                : $requestParamsPath .= "&" . $key . "=" . urlencode($valueItem);
                    }
                } else if ($value !== null && $value !== "") {
                    ($requestParamsPath == "") ?
                        $requestParamsPath .= "?" . $key . "=" . urlencode($value)
                            : $requestParamsPath .= "&" . $key . "=" . urlencode($value);
                }
            }
        }
        
        //Send the request
        try {
            $requestRes = $this->client->request(
                    'GET',
                    $this->serviceName . $subService . $requestParamsPath,
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $sessionToken
                        ],
                        'body' => $body
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) { //Errors
            return json_decode($e->getResponse()->getBody());  
        } catch (\GuzzleHttp\Exception\ConnectException $e) { //Server connection errors
            return WEB_SERVICE_CONNECTION_ERROR_MESSAGE;
        } catch (\GuzzleHttp\Exception $e) {
            return "Other exception : " . $e->getResponse()->getBody();
        }
        
        return json_decode($requestRes->getBody());
    }
}