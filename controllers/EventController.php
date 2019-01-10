<?php
//******************************************************************************
//                          EventController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: Jan, 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * @see yii\web\Controller
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventController extends Controller {
    
    const PROPERTIES = "properties";
    const RELATION = "relation";
    const VALUE = "value";
    const RDF_TYPE = "rdfType";
    const URI = "uri";
    
    /**
     * list all the events
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \app\models\yiiModels\EventSearch();
        
        //Get the search params and update pagination
        $searchParams = Yii::$app->request->queryParams;
        if (isset($searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE])) {
            $searchParams[\app\models\yiiModels\YiiModelsConstants::PAGE]--;
        }

        $searchResult = $searchModel->search(Yii::$app->session['access_token'], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === \app\models\wsModels\WSConstants::TOKEN) {
                return $this->redirect(Yii::$app->urlManager->createUrl("site/login"));
            } else {
                return $this->render('/site/error', [
                        'name' => Yii::t('app/messages','Internal error'),
                        'message' => $searchResult]);
            }
        } else {
            
            return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $searchResult
            ]);
        }
    }
}
