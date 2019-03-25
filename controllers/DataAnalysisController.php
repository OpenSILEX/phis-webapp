<?php

//******************************************************************************
//                                       DataAnalysisController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 21 feb 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\models\yiiModels\DataAnalysisAppSearch;
use app\models\yiiModels\DataAnalysisApp;
use app\models\wsModels\WSConstants;

include_once '../config/web_services.php';

/**
 * Implements the controller for available statistical and visualisation programs
 * @see yii\web\Controller
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisController extends \yii\web\Controller {
    /**
     * Constants used to parse configuration file
     */
    const INTEGRATED_FUNCTIONS = "integratedFunctions";
    const FORM_PARAMETERS = "formParameters";
    /**
     * define the behaviors
     * @return array
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ]
            ]
        ];
    }

    /**
     * List all R apps
     * @return mixed
     */
    public function actionIndex($integrated = false) {

        $searchModel = new DataAnalysisAppSearch();

        $searchParams = Yii::$app->request->queryParams;

        $searchResult = $searchModel->search($searchParams);
        if (empty($searchResult)) {
            return $this->render('error', [
                        'message' => 'No application available.'
            ]);
        } else {
            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        'integrated' => $integrated
                            ]
            );
        }
    }

    /**
     * Show standalone Demo R app
     * @return type
     */
    public function actionViewDemo() {
        $searchModel = new DataAnalysisAppSearch();
        $appDemoInformation = $searchModel->getAppDemoInformation();
        if (!empty($appDemoInformation)) {
            $this->redirect($appDemoInformation[DataAnalysisAppSearch::APP_INDEX_HREF]);
        } else {
            return $this->render('error', [
                        'message' => 'Demo application not available.'
            ]);
        }
    }

    /**
     * Show standalone Demo R app
     * @return type
     */
    public function actionView() {
        $searchParams = Yii::$app->request->queryParams;
        return $this->render('iframe-view', [
                    'appUrl' => $searchParams["url"],
                    'appName' => $searchParams["name"]
                        ]
        );
    }
    
    /**
     * Run specific rpackage function
     * @param type $rpackage
     * @param type $function
     * @return type
     */
    public function actionRunScript($rpackage = null, $function = null) {
        // test parameters
        if (!isset($rpackage) && !isset($function)) {
            Yii::$app->session->setFlash('scriptNotAvailable');
            return $this->redirect(['data-analysis/index', 'integrated' => true]);
        }

        // load package configuration
        $searchModel = new DataAnalysisAppSearch();
        $appConfiguration = $searchModel->getAppConfiguration($rpackage);

        if (!isset($appConfiguration[self::INTEGRATED_FUNCTIONS][$function])) {
            Yii::$app->session->setFlash('scriptNotAvailable');
            return $this->redirect(['data-analysis/index', 'integrated' => true]);
        }

        // get yii2 form information
        $inputParameters = $appConfiguration[self::INTEGRATED_FUNCTIONS][$function][self::FORM_PARAMETERS];
        // get form parameters keys
        $parameters = array_keys($inputParameters);
        // get data from R function (special values like varaibles lists)
        $valueParameters = $this->fillParametersFromR($searchModel, $inputParameters, $rpackage);

        // fill model
        $model = new DataAnalysisApp($parameters, [], $inputParameters);
        $model->token = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $model->wsUrl = WS_PHIS_PATH;
        
        $functionConfiguration = $appConfiguration[self::INTEGRATED_FUNCTIONS][$function];
        
        
        // load model data
        if ($model->load(Yii::$app->request->post())) {
            $session = $searchModel->ocpuserver->makeAppCall($rpackage, $function, $model->getAttributesForHTTPClient());

            // exportGrid Save parameters
            $exportGridTemporaryParameters = $model->getAttributesForHTTPClient();
            unset($exportGridTemporaryParameters["wsUrl"]);
            unset($exportGridTemporaryParameters["token"]);
            
            // error managment
            $exception = $searchModel->ocpuserver->getServerCallStatus()->getException();
            if($exception != null){
                $message = $exception->getMessage();
                Yii::$app->session->setFlash("scriptDidNotWork", $message);
            }

            $plotConfigurations = [];
            $plotWidgetUrls = [];
            $dataGrids = [];
            // get called function configuration
            
            $this->getDataFromRfunctionCall(
                    $searchModel, $functionConfiguration, $rpackage,
                    $session, $inputParameters, $plotConfigurations, 
                    $plotWidgetUrls, $dataGrids
                    );           
            
            return $this->render('run-script', [
                        'rpackage' => $rpackage,
                        'function' => $function,
                        'model' => $model,
                        'inputParameters' => $inputParameters,
                        'valueParameters' => $valueParameters,
                        'appConfiguration' => $appConfiguration,
                        'functionConfiguration' => $functionConfiguration,
                        'plotConfigurations' => $plotConfigurations,
                        'plotWidgetUrls' => $plotWidgetUrls,
                        'dataGrids' => $dataGrids,
                        'exportGridParameters' =>  json_encode($exportGridTemporaryParameters, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
            ]);
        } else {
            return $this->render('run-script', [
                        'rpackage' => $rpackage,
                        'function' => $function,
                        'model' => $model,
                        'inputParameters' => $inputParameters,
                        'valueParameters' => $valueParameters,
                        'functionConfiguration' => $functionConfiguration,
                        'appConfiguration' => $appConfiguration
            ]);
        }
    }

    /**
     * Retreive json data array from a specific opencpu session
     * @param string $sessionId opencpu session Id
     * @param string $filename json file name
     * @return array
     */
    public function actionAjaxSessionJsonFileData($sessionId, $filename) {
        if (Yii::$app->request->isAjax) {
            $searchModel = new DataAnalysisAppSearch();
            $session = new \openSILEX\opencpuClientPHP\classes\OCPUSession($sessionId, $searchModel->ocpuserver);
            $plotlySchema = $session->getFileData($filename, $session::OPENCPU_SESSION_FILE_JSON_FORMAT);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $plotlySchema;
        }
    }
    /**
     * Return ajax specific json content for datatable plugin
     * @param type $sessionId opencpu session
     * @param type $dataId index of a R data list 
     * @return array array of value
     */
    public function actionAjaxSessionGetData($sessionId,$dataId) {
        $searchModel = new DataAnalysisAppSearch();
        $session = new \openSILEX\opencpuClientPHP\classes\OCPUSession($sessionId, $searchModel->ocpuserver->getOpenCPUWebServerClient());
        $value = $session->getVal($session::OPENCPU_SESSION_JSON_FORMAT);
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(isset($dataId)){
           return $value[$dataId];
        }else{
            return $value;
        }
        
    }


    /**
     * Get values from executed function
     * @param DataAnalysisAppSearch $searchModel
     * @param array $parameters
     * @return array
     */
    private function fillParametersFromR($searchModel, $parameters, $appName) {
        $valueParameters = [];
        $rcallParameters = [];
        $rcallParameters["token"] = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $rcallParameters["wsUrl"] = WS_PHIS_PATH;
        foreach ($parameters as $key => $parameterOptions) {
            if (array_key_exists("RfunctionValues", $parameterOptions)) {
                $session = $searchModel->ocpuserver->makeAppCall($appName, $parameterOptions["RfunctionValues"], $rcallParameters);
                if($session != null){
                    $valueParameters[$key] = $session->getVal($session::OPENCPU_SESSION_JSON_FORMAT);
                    $tmp_array = [];
                    foreach ($valueParameters[$key] as $value) {
                        $tmp_array[$value["uri"]] = $value["label"];
                    }
                    $valueParameters[$key] = $tmp_array;
                }
            }
        }
        return $valueParameters;
    }

    /**
     * Retreive data, create plot and data grid 
     * @param DataAnalysisAppSearch $searchModel 
     * @param array $functionConfiguration function configuration
     * @param string $rpackage r package
     * @param \openSILEX\opencpuClientPHP\classes\OCPUSession $session opencpu session
     * @param array $inputParameters form input parameters
     * @param array $plotConfigurations plot configuration
     * @param array $plotWidgetUrls plot widget generated
     * @param array $dataGrids data grid
     */
    private function getDataFromRfunctionCall($searchModel, $functionConfiguration, $rpackage, $session, $inputParameters, &$plotConfigurations, &$plotWidgetUrls, &$dataGrids) {
        // error
        if ($searchModel->ocpuserver->getServerCallStatus()->getStatus() != 200) {
            $errorMessage = $searchModel->ocpuserver->getServerCallStatus()->getMessage();
            Yii::$app->session->setFlash("scriptDidNotWork", $errorMessage);
        } else {
            // graphic or grid function
            if ($functionConfiguration["type"] === "graphic") {
                $plotConfigurations[] = $session->getExistingFileUrl("plotlySchema", $session::OPENCPU_SESSION_FILE_JSON_FORMAT);
                $plotWidgetUrls[] = $session->getExistingFileUrl("plotWidget.html");
                // graphic or grid function    
                if (isset($functionConfiguration["linkedFunctions"])) {
                    foreach ($functionConfiguration["linkedFunctions"] as $linkedFunction => $linkedFunctionParameters) {
                        $linkedModel = new DataAnalysisApp($linkedFunctionParameters["parameters"], [], $inputParameters);
                        $linkedModel->load(Yii::$app->request->post());
                        $linkedFunctionsSession = $searchModel->ocpuserver->makeAppCall($rpackage, $linkedFunctionParameters["name"], $linkedModel->getAttributesForHTTPClient());
                        if ($linkedFunctionParameters["type"] === "grid") {
                            $data = $linkedFunctionsSession->getVal($session::OPENCPU_SESSION_JSON_FORMAT);
                            if (count($data) !== 0) {
                                foreach ($data as $dataId => $idDataArray) {
                                    $columnNames = array_keys($idDataArray[0]);
                                    $dataGrids[] = ["sessionId" => $linkedFunctionsSession->sessionId, "dataId" => $dataId,"data" => $data, "columnNames" => $columnNames];
                                }
                            }
                        }
                        if ($linkedFunctionParameters["type"] === "graphic") {
                            $plotConfigurations[] = $linkedFunctionsSession->getExistingFileUrl("plotlySchema", $session::OPENCPU_SESSION_FILE_JSON_FORMAT);
                            $plotWidgetUrls[] = $linkedFunctionsSession->getExistingFileUrl("plotWidget.html");
                        }
                    }
                }
            }
        }
    }

}
