<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\yiiModels\ContactForm;
use app\models\yiiModels\YiiUserModel;
use \app\models\wsModels\WSConstants;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Get user's groups list
     */
    private function getLoggedUsersGroups() {
        $userModel = new YiiUserModel();
        $userModel->findByEmail(Yii::$app->session['access_token'], Yii::$app->session['email']);
        Yii::$app->session['groups'] = $userModel->groups;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {

        if (!Yii::$app->session['isGuest'] && Yii::$app->session['isGuest'] != null) {
            return $this->goHome();
        }

        $model = new \app\models\yiiModels\YiiTokenModel();

         if ($model->load(Yii::$app->request->post())) {
             $model->password = md5($model->password);
             if ($model->login()) {
                $this->getLoggedUsersGroups();
                return $this->goHome();
             } else {
                 Yii::$app->getSession()->setFlash('error', Yii::t('app/messages','Bad email / password'));
             }
        }

        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * Login with ajax action when token is expired
     *
     * @return json string
     */
    public function actionLoginAjax() {
        $model = new \app\models\yiiModels\YiiTokenModel();

        // Load POST parameters
        if ($model->load(Yii::$app->request->post())) {
            $model->password = md5($model->password);

            // Get the previous and new user email to check if the same credentials have been submited
            $previousMail = Yii::$app->session['email'];
            $newMail = $model->email;

            if ($model->login()) {
                // Success
                $this->getLoggedUsersGroups();
                $result["success"] = true;
                $result["sameUser"] = ($previousMail == $newMail);
                $result["tokenTimeout"] = 0;
                if (isset($_COOKIE[WSConstants::TOKEN_COOKIE_TIMEOUT])) {
                    $result["tokenTimeout"] = $_COOKIE[WSConstants::TOKEN_COOKIE_TIMEOUT];
                }
             } else {
                 // Error bad login/pass
                 $result["success"] = false;
                 $result["error"] = Yii::t('app/messages','Bad email / password');
             }
        } else {
            // Unknown error
            $result["success"] = false;
            $result["error"] = Yii::t('app/messages','Unknown error');
        }

        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    /**
     * logout a user
     * @return Response redirection to the index page of the website
     */
    public function actionDisconnect() {
        Yii::$app->session['access_token'] = null;
        Yii::$app->session['email'] = null;
        Yii::$app->session['isGuest'] = true;
        Yii::$app->session['groups'] = null;

        // Remove cookie containing token timeout
        setcookie(
            WSConstants::TOKEN_COOKIE_TIMEOUT,
            null
        );

        return $this->redirect(Yii::$app->urlManager->createUrl("site/index"));
    }


    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
      #  $model = new ContactForm();
      #  if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
      #      Yii::$app->session->setFlash('contactFormSubmitted');

      #      return $this->refresh();
      #  }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }


    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Change the current language. And redirect to the translated actual page
     * @param string $language the new language
     */
    public function actionLanguage($language) {
        SiteController::changeLanguage($language);
        $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @action update website language
     * @param string $language the new language
     */
    public static function changeLanguage($language) {
        Yii::$app->language = $language;
        $languageCookie = new \yii\web\Cookie([
            'name' => 'language',
            'value' => $language,
            'expire' => time() + 60 * 60 * 24 * 30, // 30 days
        ]);
        Yii::$app->response->cookies->add($languageCookie);
    }

    /**
     * render the page with the description of the phis vocabulary
     * @return string
     */
    public function actionOntology() {
        return $this->render('ontology');
    }
}
