<?php
//******************************************************************************
//                        GenericController.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 15 Apr. 2098
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use app\models\wsModels\WSConstants;

/**
 * Web controller with generic functions.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class GenericController extends \yii\web\Controller
{   
    /**
     * Tells if a web service request returned the token constant.
     * @param type $requestResults
     * @return type
     */
    private function doesWebserviceRequestReturnTokenConstant($requestResults) {
        return is_string($requestResults) && $requestResults === WSConstants::TOKEN;
    }
    
    /**
     * Redirects to the login page.
     */
    private function redirectToLoginPage() {
        $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
    }
    
    /**
     * Renders a page when an error occured during a request.
     */
    private function renderPageErrorWhenWebServiceInternalError($requestResult) {
        $this->render(SiteMessages::SITE_ERROR_PAGE_ROUTE, [
            'name' => Yii::t('app/messages','Internal error'),
            'message' => $requestResult->{WSConstants::METADATA}->{WSConstants::STATUS}[0]->{WSConstants::EXCEPTION}->{WSConstants::DETAILS}]);
    }
    
    /**
     * Tells if a request created resources or not according to its results.
     * @param type $requestResult
     */
    private function areResourceCreated($requestResult) {
        return isset($requestResult->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0]);
    }
    
    /**
     * Redirects to first created resource.
     */
    private function redirectToFirstCreatedResource($requestResult) {
        return $this->redirect(['view', 'id' => $requestResult->{WSConstants::METADATA}->{WSConstants::DATA_FILES}[0]]);
    }
    
    /**
     * Handles a POST response from a web service.
     * @param type $requestResults
     * @param type $objectReturnUrl
     * @return type
     */
    protected function handlePostResponse($requestResults, $objectReturnUrl) {
        if ($this->doesWebserviceRequestReturnTokenConstant($requestResults)) {
            return $this->redirectToLoginPage();
        } else {
            if ($this->areResourceCreated($requestResults)) { // resource created
                if ($objectReturnUrl) {
                    $this->redirect($objectReturnUrl);
                } else {
                    return $this->redirectToFirstCreatedResource($requestResults);
                }                    
            } else { // an error occurred
                return $this->renderPageErrorWhenWebServiceInternalError($requestResults);
            }
        }
    }
}
