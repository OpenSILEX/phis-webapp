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
 * DataAnalysisAppSearch represents the model behind the search form 
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisAppSearch {
    /**
     *
     * @var OpenCPUServer 
     */
    public $ocpuserver;

    const VIGNETTE_IMAGE = "vignette";
    const APP_SHORT_NAME = "appShortName";
    const R_PACKAGE_NAME = "packageName";
    const APP_INDEX_HREF = "appIndexHref";
    const FUNCTION_NAME = "functionName";
    const FUNCTION_HELP = "functionHelp";
    const AVAILABLE_FUNCTIONS = "availableFunctions";
    const FUNCTION_DESCRIPTION = "description";
    const INTEGRATED_FUNCTION = "integratedFunctions";


    public function __construct($verbose = false) {
        $this->ocpuserver = new OpenCPUServer(\config::path()['ocpuServer']);
        if ($verbose) {
            $this->ocpuserver::$ENABLE_CALL_STATS = true;
        }
    }

    /**
     * 
     * @param array $sessionToken used for the data access
     * @param string $params search params
     * @return mixed DataProvider of the result 
     *               or string \app\models\wsModels\WSConstants::TOKEN if the user needs to log in
     */
    public function search() {
        $apps = $this->ocpuserver->getAvailableApps();
        $visualisationsInfo = [];
        foreach ($apps as $app) {
            $appConfiguration = $this->getAppConfiguration($app);
            $availableFunctions = $appConfiguration[self::AVAILABLE_FUNCTIONS];
            $integratedFunctions = $appConfiguration[self::INTEGRATED_FUNCTION];
            foreach ($availableFunctions as $functionName) {
                $applicationWebPath = $this->ocpuserver->getOpenCPUWebServerUrl() . "apps/" . $app . "/www";
                $descriptionPath = $this->ocpuserver->getOpenCPUWebServerUrl() . "apps/" . $app . "/opensilex/descriptions";

                $descriptionText = $integratedFunctions[$functionName][self::FUNCTION_DESCRIPTION];
                $visualisationsInfo[$functionName][self::VIGNETTE_IMAGE] = "$descriptionPath/$functionName/$functionName.png";
                $visualisationsInfo[$functionName][self::FUNCTION_HELP] = $descriptionText;
                $visualisationsInfo[$functionName][self::APP_SHORT_NAME] = explode("/", $app)[1] . "-" . $functionName;
                $visualisationsInfo[$functionName][self::R_PACKAGE_NAME] = $app;
                $url = "$applicationWebPath/$functionName.html?accessToken=" . Yii::$app->session[WSConstants::ACCESS_TOKEN] . "&wsUrl=" . WS_PHIS_PATH;
                $visualisationsInfo[$functionName][self::APP_INDEX_HREF] = Url::to(['data-analysis/view', 'url' => $url, 'name' => explode("/", $app)[1]]);
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

        return [];
    }

  
     /**
     * Return R application configuration
     * @param string $app
     * @return array
     */
    function getAppConfiguration($app) {
        try {
            $response = $this->ocpuserver->getOpenCPUWebServerClient()->request(OpenCPUServer::OPENCPU_SERVER_GET_METHOD, 'apps/' . $app . '/opensilex/webAppConfig.yml');
            $body = $response->getBody();
            // retrevies body as a string
            $stringBody = (string) $body;

            return Yaml::parse($stringBody);
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

        return [];
    }
}
