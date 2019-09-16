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
use app\models\yiiModels\ScientificAppSearch;
use app\models\wsModels\WSConstants;

include_once '../config/web_services.php';

/**
 * Implements the controller for available statistical and visualisation programs
 * @see yii\web\Controller
 * @author Arnaud Charleroy <arnaud.charleroy@inra.fr>
 */
class DataAnalysisController extends GenericController {

    /**
     * Define the behaviors of the controller
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
     * Show in a gallery all available R applications
     * @return string result of a view
     */
    public function actionIndex() {
        $searchModel = new ScientificAppSearch();

        $searchParams = Yii::$app->request->queryParams;

        $searchResult = $searchModel->search(
                Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams
        );
        $shinyServerStatus = $searchModel->shinyProxyServerStatus(
                Yii::$app->session[WSConstants::ACCESS_TOKEN]
        );
      
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN_INVALID) {
                return $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
            } else {
                return $this->render(SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                            SiteMessages::SITE_PAGE_NAME => SiteMessages::INTERNAL_ERROR,
                            SiteMessages::SITE_PAGE_MESSAGE => $searchResult]);
            }
        } else {
            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $searchResult,
                        'shinyServerStatus' => $shinyServerStatus,
                            ]
            );
        }
    }

    /**
     * Displays a single annotation model.
     * @return mixed
     */
    public function actionView($url) {
        return $this->render('view',
                    [
                        'url' => $url,
                    ]
        );
    }

}