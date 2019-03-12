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
     * List all Apps
     * @return mixed
     */
    public function actionIndex($integrated = false) {

        $searchModel = new DataAnalysisAppSearch();

        $searchParams = Yii::$app->request->queryParams;

        $searchResult = $searchModel->search($searchParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $searchResult,
                    'integrated' => $integrated
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

        if (!isset($appConfiguration[$function])) {
            Yii::$app->session->setFlash('scriptNotAvailable');
            return $this->redirect(['data-analysis/index', 'integrated' => true]);
        }

        // get yii2 form information
        $formParameters = $appConfiguration["$function"]["formParameters"];

        // get form parameters keys
        $parameters = array_keys($formParameters);
        // get data from R function (special values like varaibles lists)
        $parametersValues = $this->fillParametersFromR($searchModel, $formParameters, $rpackage);

        // fill model
        $model = new DataAnalysisApp($parameters, [], $formParameters);
        $model->token = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $model->wsUrl = WS_PHIS_PATH;
        // load model data
        if ($model->load(Yii::$app->request->post())) {
            $session = $searchModel->ocpuserver->makeAppCall($rpackage, $function, $model->getAttributesForHTTPClient());

            $plotConfigurations = [];
            $plotWidgetUrls = [];
            $dataGrids = [];
            // get called function configuration
            $functionConfiguration = $appConfiguration["$function"];
            $this->getDataFromRfunctionCall(
                    $searchModel, $functionConfiguration, $rpackage, $function, $session, $formParameters, $plotConfigurations, $plotWidgetUrls, $dataGrids);
           
            // exportGrid Save parameters
            $exportGridTemporaryParameters = $model->getAttributesForHTTPClient();
            unset($exportGridTemporaryParameters["wsUrl"]);
            unset($exportGridTemporaryParameters["token"]);


            return $this->render('form', [
                        'rpackage' => $rpackage,
                        'function' => $function,
                        'model' => $model,
                        'parameters' => $formParameters,
                        'parametersValues' => $parametersValues,
                        'appConfiguration' => $appConfiguration,
                        'plotConfigurations' => $plotConfigurations,
                        'plotWidgetUrls' => $plotWidgetUrls,
                        'dataGrids' => $dataGrids,
                        'exportGridParameters' => 'Search parameters' . json_encode($exportGridTemporaryParameters)
            ]);
        } else {
            return $this->render('form', [
                        'rpackage' => $rpackage,
                        'function' => $function,
                        'model' => $model,
                        'parameters' => $formParameters,
                        'parametersValues' => $parametersValues,
                        'appConfiguration' => $appConfiguration
            ]);
        }
    }

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

    public function actionView() {
        $searchParams = Yii::$app->request->queryParams;
        return $this->render('iframe-view', [
                    'appUrl' => $searchParams["url"],
                    'appName' => $searchParams["name"]
                        ]
        );
    }

    /**
     * 
     * @param DataAnalysisAppSearch $searchModel
     * @param array $parameters
     * @return array
     */
    private function fillParametersFromR($searchModel, $parameters, $appName) {
        $parametersValues = [];
        $rcallParameters = [];
        $rcallParameters["token"] = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $rcallParameters["wsUrl"] = WS_PHIS_PATH;
        foreach ($parameters as $key => $parameterOptions) {
            if (array_key_exists("RfunctionValues", $parameterOptions)) {
                $session = $searchModel->ocpuserver->makeAppCall($appName, $parameterOptions["RfunctionValues"], $rcallParameters);
                $parametersValues[$key] = $session->getVal($session::OPENCPU_SESSION_JSON_FORMAT);
                // special case for listVariables
                $tmp_array = [];
                foreach ($parametersValues[$key] as $value) {
                    $tmp_array[$value["value"]] = $value["name"];
                }
                $parametersValues[$key] = $tmp_array;
                // \\ special case for listVariables
            }
        }
        return $parametersValues;
    }

    /**
     * 
     * @param type $searchModel
     * @param type $functionConfiguration
     * @param type $rpackage
     * @param type $function
     * @param type $session
     * @param type $formParameters
     * @param type $plotConfigurations
     * @param type $plotWidgetUrls
     * @param type $dataGrids
     */
    private function getDataFromRfunctionCall($searchModel, $functionConfiguration, $rpackage, $function, $session, $formParameters, &$plotConfigurations, &$plotWidgetUrls, &$dataGrids) {
        // error
        if ($searchModel->ocpuserver->getServerCallStatus()->getStatus() != 200) {
            $errorMessage = $searchModel->ocpuserver->getServerCallStatus()->getMessage();
            Yii::$app->session->setFlash("scriptDidNotWork", $errorMessage);
        } else {
            // graphic or grid function
            if ($functionConfiguration["type"] === "graphic") {
                $plotConfigurations[] = $session->getExistingFileUrl("plotlySchema", $session::OPENCPU_SESSION_FILE_JSON_FORMAT);
                $plotWidgetUrls[] = $session->getExistingFileUrl($function . "Widget.html");
                // graphic or grid function    
                if (isset($functionConfiguration["linkedFunctions"])) {
                    foreach ($functionConfiguration["linkedFunctions"] as $linkedFunction => $linkedFunctionParameters) {
                        $linkedModel = new DataAnalysisApp($linkedFunctionParameters["parameters"], [], $formParameters);
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
                            $plotWidgetUrls[] = $linkedFunctionsSession->getExistingFileUrl($linkedFunction . "Widget.html");
                        }
                    }
                }
            }
        }
    }

}
