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
use openSILEX\opencpuClientPHP\OpenCPUServer;
use app\models\wsModels\WSConstants;
use yii\helpers\Url;
use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use openSILEX\opencpuClientPHP\classes\CallStatus;

include_once '../config/web_services.php';
require_once '../config/config.php';

/**
 * DataAnalysisAppSearch search class which makes link
 * between openSILEX and OpenCPU
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisAppSearch {
    /**
     *
     * @var OpenCPUServer 
     */
    public $ocpuserver;
    
    /**
     * Application information directory
     */
    const APP_DESCRIPTION_DIRECTORY = "opensilex";

    /**
     * Metadata application constants
     */
    const APP_VIGNETTE_IMAGE = "appVignette";
    const APP_INDEX_URL = "appUrlIndex";
    const APP_DESCRIPTION = "description";
    const APP_SHORT_NAME = "appShortName";
    const APP_NAME = "appName";
    
    /**
     * Fixed default demo application path (similar to github link)
     */
    const DEFAULT_TEST_DEMO_APP = "opensilex/opensilex-datavis-rapp-demo";

    /**
     * Default description not found
     */
    const DESCRIPTION_NOT_FOUND = "No description found.";
    
    /**
     * Initialize openCPU server connection
     * @param boolean $verbose if true, give connection metrics informations
     */
    public function __construct($verbose = false) {
        $this->ocpuserver = new OpenCPUServer(\config::path()['ocpuServer']);
        if ($verbose) {
            $this->ocpuserver::$ENABLE_CALL_STATS = true;
        }
    }

    /**
     * Remove the choosen application from an application list (demo app)
     * @param string $appName the name of the application
     * @param array $appList a list of app (github repository path list )
     * @return array list without self::DEFAULT_DEMO_APP app
     */
    private function removeAppFromAppList($appName, $appList) {
        return \array_diff($appList,[$appName]);
    }
    
    /**
     * List all available R applications
     * This function creates all the necessary links to
     * include R application in OpenSILEX web application 
     * 
     * @param array $sessionToken used for the data access
     * @param string $params search params
     * @return array list of app with their informations
     */
    public function search() {
        $serverUrl = $this->ocpuserver->getOpenCPUWebServerUrl();
        $appList = $this->ocpuserver->getAvailableApps();
        $applications = $this->removeAppFromAppList(self::DEFAULT_TEST_DEMO_APP, $appList);

        $applicationsMetaData = [];

        foreach ($applications as $application) {
            $applicationMetaData = $this->getApplicationInformation($serverUrl, $application);
            $applicationsMetaData = array_merge($applicationsMetaData, $applicationMetaData);
        }
        
        return $applicationsMetaData;
    }

    /**
     * A base R application structure :
     * 
     * Rapplication
     *   ├── comparevariablesdemo.Rproj
     *   ├── DESCRIPTION
     *   ├── inst
     *   │   ├── opensilex (informations to make a link with OpenSILEX)
     *   │   │    ├── description.md
     *   │   │    └── vignette.png
     *   │   │   
     *   │   └── www (web application)
     *   │       ├── css
     *   │       ├── index.html
     *   │       └── js
     *   ├── man R (documentation files)
     *   │    └── doc.Rd
     *   ├── NAMESPACE
     *   ├── R (R functions)
     *   │   ├── functions.R
     *   └── README.md
     * Retreive application informations according to application structure
     * above
     * @return array 
     */
    private function getApplicationInformation($application) {
        $serverUrl = $this->ocpuserver->getOpenCPUWebServerUrl();

        $appMetaData = [];
        if ($this->ocpuserver->status()) {
            $applicationWebPath = $serverUrl . "apps/" . $application . "/www";
            $descriptionPath = $serverUrl . "apps/" . $application . "/" . self::APP_DESCRIPTION_DIRECTORY;

            $existVignette = $this->existsRemoteFile("$descriptionPath/vignette.png");
            if ($existVignette) {
                $appMetaData[$application][self::APP_VIGNETTE_IMAGE] = "$descriptionPath/vignette.png";
            } else {
                $notFoundImage = Yii::getAlias('@web') . "/images/logos/R_logo.png";
                $appMetaData[$application][self::APP_VIGNETTE_IMAGE] = $notFoundImage;
            }
            
            $descriptionVignette = $this->existsRemoteFile("$descriptionPath/description.md");
            if ($descriptionVignette) {
                $appMetaData[$application][self::APP_DESCRIPTION] = "$descriptionPath/description.md";
            } else {
                $appMetaData[$application][self::APP_DESCRIPTION] = self::DESCRIPTION_NOT_FOUND;
            }
            
            $appMetaData[$application][self::APP_SHORT_NAME] = explode("/", $application)[1];
            
            $appMetaData[$application][self::APP_NAME] = $application;
            
            $url = "$applicationWebPath/index.html?accessToken=" . Yii::$app->session[WSConstants::ACCESS_TOKEN] . "&wsUrl=" . WS_PHIS_PATH;
            $urlToApplication = Url::to(
                            [
                                'data-analysis/view',
                                'url' => $url,
                                'name' => explode("/", $application)[1]
                            ]
            );
            $appMetaData[$application][self::APP_INDEX_URL] = $urlToApplication;
        }
        return $appMetaData;
    }

    /**
     * Retreive information on demo app only
     * @return array information on demo app
     */
    public function getAppDemoInformation() {
        $visualisationsInfo = [];

        if ($this->ocpuserver->status()) {
            $app = self::DEFAULT_TEST_DEMO_APP;

            $appConfiguration = $this->getAppConfiguration($app);
            if (!empty($appConfiguration) 
                    && isset($appConfiguration[self::AVAILABLE_FUNCTIONS][0]) 
                    && isset($appConfiguration[self::INTEGRATED_FUNCTION])) {
                // only one function
                $functionName = $appConfiguration[self::AVAILABLE_FUNCTIONS][0];
                $integratedFunctions = $appConfiguration[self::INTEGRATED_FUNCTION];

                $applicationWebPath = $this->ocpuserver->getOpenCPUWebServerUrl() . "apps/" . $app . "/www";
                $descriptionPath = $this->ocpuserver->getOpenCPUWebServerUrl() . "apps/" . $app . "/opensilex/description";

                $descriptionText = $integratedFunctions[$functionName][self::FUNCTION_DESCRIPTION];
                $visualisationsInfo[self::APP_VIGNETTE_IMAGE] = "$descriptionPath/$functionName.png";
                $visualisationsInfo[self::FUNCTION_HELP] = $descriptionText;
                $visualisationsInfo[self::APP_SHORT_NAME] = explode("/", $app)[1] . "-" . $functionName;
                $visualisationsInfo[self::APP_NAME] = $app;
                $url = "$applicationWebPath/index.html?accessToken=" . Yii::$app->session[WSConstants::ACCESS_TOKEN] . "&wsUrl=" . WS_PHIS_PATH;
                $visualisationsInfo[self::APP_INDEX_URL] = Url::to(['data-analysis/view', 'url' => $url, 'name' => explode("/", $app)[1]]);
            }
        }
        return $visualisationsInfo;
    }

    /**
     * Return exported functionalities 
     * @param string $applicationName 
     * @return array functionalities
     */
    function getAppFunctionalities($applicationName) {
         if ($this->ocpuserver->status()) {
            try {
                $response = $this->ocpuserver->getOpenCPUWebServerClient()->request(OpenCPUServer::OPENCPU_SERVER_GET_METHOD, 'apps/' . $applicationName . '/opensilex/descriptions');
                $body = $response->getBody();
                // retrevies body as a string
                $stringBody = (string) $body;
                $sessionValuesResults = explode("\n", $stringBody);
                $cleansessionValuesResults = array_filter($sessionValuesResults);

                return $cleansessionValuesResults;
            } catch (RequestException $e) {
                $errorMessage = Psr7\str($e->getRequest());
                if ($e->hasResponse()) {
                    $errorMessage .= '--' . Psr7\str($e->getResponse());
                }
                $this->serverCallStatus = new CallStatus($errorMessage, $e->getResponse()->getStatusCode(), $e);
                // ClientException is thrown for 400 level errors
            } catch (ClientException $e) {
                $errorMessage = Psr7\str($e->getRequest());
                if ($e->hasResponse()) {
                    $errorMessage .= '--' . Psr7\str($e->getResponse());
                }
                $this->serverCallStatus = new CallStatus($errorMessage, $e->getResponse()->getStatusCode(), $e);
                // is thrown for 500 level errors
            } catch (ServerException $e) {
                $errorMessage = Psr7\str($e->getRequest());
                if ($e->hasResponse()) {
                    $errorMessage .= '--' . Psr7\str($e->getResponse());
                }
                $this->serverCallStatus = new CallStatus($errorMessage, $e->getResponse()->getStatusCode(), $e);
            }
        }
        return [];
    }

  
     /**
     * Return R application configuration
     * @param string $app
     * @return array
     */
    function getAppConfiguration($app) {
        if ($this->ocpuserver->status()) {
            try {
                $response = $this->ocpuserver->getOpenCPUWebServerClient()->request(OpenCPUServer::OPENCPU_SERVER_GET_METHOD, 'apps/' . $app . '/opensilex/webAppConfig.yml');

                $body = $response->getBody();
                // retrevies body as a string
                $stringBody = (string) $body;

                return Yaml::parse($stringBody);
                } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
                    return [];            
                } catch (RequestException $e) {
                $errorMessage = Psr7\str($e->getRequest());
                if ($e->hasResponse()) {
                    $errorMessage .= '--' . Psr7\str($e->getResponse());
                }
                $this->serverCallStatus = new CallStatus($errorMessage, $e->getResponse()->getStatusCode(), $e);
                // ClientException is thrown for 400 level errors
            } catch (ClientException $e) {
                $errorMessage = Psr7\str($e->getRequest());
                if ($e->hasResponse()) {
                    $errorMessage .= '--' . Psr7\str($e->getResponse());
                }
                $this->serverCallStatus = new CallStatus($errorMessage, $e->getResponse()->getStatusCode(), $e);
                // is thrown for 500 level errors
            } catch (ServerException $e) {
                $errorMessage = Psr7\str($e->getRequest());
                if ($e->hasResponse()) {
                    $errorMessage .= '--' . Psr7\str($e->getResponse());
                }
                $this->serverCallStatus = new CallStatus($errorMessage, $e->getResponse()->getStatusCode(), $e);
            }
        }
        return [];
    }
    
    function existsRemoteFile($url) {
        try {
            $this->ocpuserver->getOpenCPUWebServerClient()->head($url);
            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return false;
        }
    }

}

   
