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
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use openSILEX\opencpuClientPHP\classes\CallStatus;

include_once '../config/web_services.php';
require_once '../config/config.php';

/**
 * DataAnalysisAppSearch search class which makes link
 * between OpenSILEX and OpenCPU apps
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisAppSearch {
    /**
     * @var OpenCPUServer 
     */
    public $ocpuserver;
    
    /**
     * Application information directory.
     */
    const APP_DESCRIPTION_DIRECTORY = "opensilex";

    /**
     * Metadata application constants to 
     * define application informations array keys.
     * For more explanation,
     * see getApplicationInformation() function.
     */
    /**
     * path of the vignette image.
     */
    const APP_VIGNETTE_PATH = "appVignette";

    /**
     * Application index url.
     */
    const APP_INDEX_URL = "appUrlIndex";

    /**
     * Application description.md file content
     * or default message.
     */
    const APP_DESCRIPTION = "appDescription";

    /**
     * Application description.md file content
     * or default message.
     */
    const APP_SHORT_NAME = "appShortName";
    /**
     * Application name (github path)
     * without provider name.
     */
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
     * List all available R applications
     * This function creates all the necessary links to
     * include R application in OpenSILEX web application 
     * 
     * @param array $sessionToken used for the data access
     * @param array $params search params (maybe used to filter apps)
     * @return array list of app with their informations
     */
    public function search($params = null) {
        $appList = $this->ocpuserver->getAvailableApps();
        // list all applications
        $applications = $this->removeAppFromAppList(self::DEFAULT_TEST_DEMO_APP, $appList);

        $applicationsMetaData = [];
        // retreive each informations on each applications
        foreach ($applications as $application) {
            $applicationMetaData = $this->getApplicationInformation($application);
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
     * @return array application metadata
     * 
     * ["niio972/opensilex-dataviz-compare-variables"]=> { 
     *        ["appVignette"]=> "/phis-webapp/web/images/logos/R_logo.png" 
     *        ["appDescription"]=> "A demo application." 
     *        ["appShortName"]=> "opensilex-dataviz-compare-variables" 
     *        ["appName"]=> "niio972/opensilex-dataviz-compare-variables" 
     *        ["appUrlIndex"]=> "/phis-webapp/web/index.php?r=data-analysis%2Fview....
     *  }
     */
    public function getApplicationInformation($application) {
        $serverUrl = $this->ocpuserver->getOpenCPUWebServerUrl();

        $appMetaData = [];
        if ($this->ocpuserver->status()) {
            $applicationWebPath = $serverUrl . "apps/" . $application . "/www";
            $descriptionPath = $serverUrl . "apps/" . $application . "/" . self::APP_DESCRIPTION_DIRECTORY;
            $existVignette = $this->existsRemoteFile("$descriptionPath/vignette.png");
            // test if the vignette.png image exist 
            if ($existVignette) {
                $appMetaData[$application][self::APP_VIGNETTE_PATH] = "$descriptionPath/vignette.png";
            } else {
                $notFoundImage = Yii::getAlias('@web') . "/images/logos/R_logo.png";
                $appMetaData[$application][self::APP_VIGNETTE_PATH] = $notFoundImage;
            }

            $descriptionVignette = $this->existsRemoteFile("$descriptionPath/description.md");
            // test if the description.md file exist
            if ($descriptionVignette) {
                $description = $this->getRemoteMarkdownFileDescription($application);
                // not empty description file 
                if (isset($description) && !empty($description)) {
                    $appMetaData[$application][self::APP_DESCRIPTION] = $description;
                    // empty description file 
                } else {
                    $appMetaData[$application][self::APP_DESCRIPTION] = Yii::t('app/messages', self::DESCRIPTION_NOT_FOUND);
                }
                // description file doesn't exist
            } else {
                $appMetaData[$application][self::APP_DESCRIPTION] = Yii::t('app/messages', self::DESCRIPTION_NOT_FOUND);
            }

            $appMetaData[$application][self::APP_SHORT_NAME] = explode("/", $application)[1];

            $appMetaData[$application][self::APP_NAME] = $application;

            $url = "$applicationWebPath/index.html?accessToken=" . Yii::$app->session[WSConstants::ACCESS_TOKEN] . "&wsUrl=" . WS_PHIS_PATH;
            $urlToApplication = Url::to(
                            [
                                'data-analysis/view',
                                'url' => $url,
                                'name' => $appMetaData[$application][self::APP_SHORT_NAME]
                            ]
            );
            $appMetaData[$application][self::APP_INDEX_URL] = $urlToApplication;
        }

        return $appMetaData;
    }
        
    /**
     * Check if a remote file is reachable
     * @param string $url any url string
     * @return boolean true if it is reachable
     *                 false if not
     */
    private function existsRemoteFile($url) {
        try {
            if ($this->ocpuserver->status()) {
                $this->ocpuserver->getOpenCPUWebServerClient()->request('GET', $url);
                return true;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            
        }
        return false;
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
     * Retreive a description file write in Markdown by a R developper
     * from the R application
     * @param string $application opencpu application name (github name)
     * @return string null if no description text in file
     *                string not empty if there is a description
     */
    function getRemoteMarkdownFileDescription($application) {
        if ($this->ocpuserver->status()) {
            try {
                $response = $this->ocpuserver->getOpenCPUWebServerClient()->request(
                        OpenCPUServer::OPENCPU_SERVER_GET_METHOD, 'apps/' . $application . '/opensilex/description.md'
                );

                $body = $response->getBody();
                // retrevies body as a string
                $stringBody = (string) $body;
                // process markdown file
                return \yii\helpers\Markdown::process($stringBody);
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
        return null;
    }
}
