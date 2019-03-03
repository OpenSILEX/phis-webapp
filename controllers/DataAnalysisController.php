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
use \yii\data\ArrayDataProvider;

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
     * List all Apps
     * @return mixed
     */
    public function actionRunScript($id = null) {
        $appNamePath = "niio972/variablesStudy";

        if (!isset($id)) {
            Yii::$app->session->setFlash('scriptNotAvailable');
            return $this->redirect(['data-analysis/index','integrated' => true]);
        }
        $searchModel = new DataAnalysisAppSearch();
        $appConfiguration = $searchModel->getAppConfiguration($appNamePath);

        if (!isset($appConfiguration["$id"])) {
            Yii::$app->session->setFlash('scriptNotAvailable');
            return $this->redirect(['data-analysis/index','integrated' => true]);
        }
        // function configuration
        $functionConfiguration = $appConfiguration["$id"];

        $formParameters = $appConfiguration["$id"]["formParameters"];

        // get parameters
        $parameters = array_keys($formParameters);
        // special values
        $parametersValues = $this->fillParametersFromR($searchModel, $formParameters, $appNamePath);

        // fill model
        $model = new DataAnalysisApp($parameters, [], $formParameters);
        $model->token = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $model->wsUrl = WS_PHIS_PATH;
        // load model data
        if ($model->load(Yii::$app->request->post())) {
            $session = $searchModel->ocpuserver->makeAppCall($appNamePath, $id, $model->getAttributesForHTTPClient());

            $plotConfigurations = [];
            $plotWidgetUrls = [];
            $dataGrids = [];

            // error
            if ($searchModel->ocpuserver->getServerCallStatus()->getStatus() != 200) {
                $errorMessage = $searchModel->ocpuserver->getServerCallStatus()->getMessage();
                Yii::$app->session->setFlash("scriptDidNotWork", $errorMessage);
            } else {
                // graphic or grid function
                if ($functionConfiguration["type"] === "graphic") {
                    $plotConfigurations[] = $session->getExistingFileUrl("plotlySchema", $session::OPENCPU_SESSION_FILE_JSON_FORMAT);
                    $plotWidgetUrls[] = $session->getExistingFileUrl($id . "Widget.html");
                // graphic or grid function    
                    if (isset($functionConfiguration["linkedFunctions"])) {
                        foreach ($functionConfiguration["linkedFunctions"] as $function => $linkedFunctionParameters) {
                            $linkedModel = new DataAnalysisApp($linkedFunctionParameters["parameters"], [], $formParameters);
                            $linkedModel->load(Yii::$app->request->post());
                            $linkedFunctionsSession = $searchModel->ocpuserver->makeAppCall($appNamePath, $linkedFunctionParameters["name"], $linkedModel->getAttributesForHTTPClient());
                            if ($linkedFunctionParameters["type"] === "grid") {
                                $data = $linkedFunctionsSession->getVal($session::OPENCPU_SESSION_JSON_FORMAT);

                                    $dataProvider = new ArrayDataProvider([
                                    'allModels' => $data,
                                    'pagination' => [
                                        'pageSize' => 10,
                                    ],
                                    'totalCount' => count($data)
                                ]);
                                if (count($data) !== 0) {
                                    $columnNames = array_keys($data[0]);
                                    $dataGrids[] = ["sessionId" => $linkedFunctionsSession->sessionId, "dataProvider" => $dataProvider, "columnNames" => $columnNames];
                                }
                            }
                            if ($linkedFunctionParameters["type"] === "graphic") {
                                $plotConfigurations[] = $linkedFunctionsSession->getExistingFileUrl("plotlySchema", $session::OPENCPU_SESSION_FILE_JSON_FORMAT);
                                $plotWidgetUrls[] = $linkedFunctionsSession->getExistingFileUrl($id . "Widget.html");
                            }
                        }
                    }
                }
            }

            return $this->render('form', [
                        'id' => $id,
                        'model' => $model,
                        'parameters' => $formParameters,
                        'parametersValues' => $parametersValues,
                        'appConfiguration' => $appConfiguration,
                        'plotConfigurations' => $plotConfigurations,
                        'plotWidgetUrls' => $plotWidgetUrls,
                        'dataGrids' => $dataGrids,
                        'sessionId' => $session->sessionId
            ]);
        } else {
            return $this->render('form', [
                        'id' => $id,
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

    public function actionView() {
        $searchParams = Yii::$app->request->queryParams;
        return $this->render('view', [
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
                foreach ($parametersValues[$key]["name"] as $keyOption => $value) {
                    $tmp_array[$parametersValues[$key]["value"][$keyOption]] = $value;
                }
                $parametersValues[$key] = $tmp_array;
                // \\ special case for listVariables
            }
        }
        return $parametersValues;
    }

}
