<?php
//**********************************************************************************************
//                                       main.php 
//
// SILEX-PHIS
// Copyright © INRA 2017
// Creation date: February 2017
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//***********************************************************************************************

/* @var $this \yii\web\View */
/* @var $content string */

use kartik\icons\Icon;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use lavrentiev\widgets\toastr\ToastrAsset;
use kartik\nav\NavX;
use \app\models\wsModels\WSConstants;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
        
require_once(__DIR__ . '/../../config/config.php');
require_once(__DIR__ . '/../../config/web_services.php');

AppAsset::register($this);
ToastrAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    //To use the fontawesome glyphicons on the page
    Icon::map($this, Icon::ICF); //@see https://icofont.com/icons
    Icon::map($this, Icon::FA);
    
    $webappName = Yii::$app->params['opensilex-webapp-type'] === "phis" ? "PHIS" : "OpenSILEX";
    $footerCopyrightWebappName = Yii::$app->params['opensilex-webapp-type'] === "phis" ? "OpenSILEX - PHIS" : "OpenSILEX";
    
    NavBar::begin([
        'brandLabel' => $webappName . ' <i> ' . Yii::$app->params['platform'] . '</i>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems;
    
    //unconnect user
    if (Yii::$app->session['isGuest'] || Yii::$app->session['isGuest'] === null) {
        if (Yii::$app->params['isDemo'] == true) {
            $menuItems = [['label' => Yii::t('app', 'Login'), 'options' => ['onclick' => "openDemoLogin(event)"]]];
        } else {
            $menuItems = [['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']]];            
        }
    // admin user
    } else if (Yii::$app->session['isAdmin']) { 
        $menuItems[] = ['label' => Yii::t('app', 'Experimental Organization'),
                        'items' => [
                            [
                                'label' => Icon::show('home', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Scientific frame} other{Scientific frames}}', ['n' => 2]), 
                                'url' => ['/infrastructure'],
                            ],
                            [
                                'label' => Icon::show('folder-open', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]),
                                'url' => ['/project/index'],
                            ],
                            [
                                'label' => Icon::show('flask', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]),
                                'url' => ['/experiment/index']
                            ],
                            [
                                'label' => Icon::show('leaf', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]),
                                'url' => ['/scientific-object/index']
                            ],
                            [
                                'label' => Icon::show('eye-open', [], Icon::BSG) . " " . Yii::t('app', 'Variables'), 
                                'url' => ['/variable/index']
                            ],
                            [
                                'label' => Icon::show('wheat', ['class' => 'icofont-lg'], Icon::ICF) . " " . Yii::t('app', 'Species'), 
                                'url' => ['/species/index']
                            ],
                            [
                                'label' => Icon::show('flag', [], Icon::FA) . " " . Yii::t('app', 'Events'), 
                                'url' => ['/event/index']
                            ],
                            [
                                'label' => Icon::show('comment', [], Icon::FA) . " " . Yii::t('app', 'Annotations'), 
                                'url' => ['/annotation/index']
                            ],                            
                        ]];
        $menuItems[] = [
                            'label' => Yii::t('app', 'Data'), 
                            'items' => [
                                [
                                    'label' => Icon::show('plus', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', 'Add'), 
                                    'url' => ['/dataset/create']
                                ],
                                [
                                    'label' => Icon::show('search', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', 'View'), 
                                    'url' => ['/data/index']
                                ]
                            ]
                        ];
        $menuItems[] = ['label' => Yii::t('app', 'Device'),
                        'items' => [
                            [
                                'label' => Icon::show('camera', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 
                                'url' => ['/sensor/index']
                            ],
                            [
                                'label' => Icon::show('bullhorn', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Actuator} other{Actuators}}', ['n' => 2]), 
                                'url' => ['/actuator/index']
                            ],
                            [
                                'label' => Icon::show('blackboard', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 
                                'url' => ['/vector/index']
                            ],
                            //SILEX:info
                            //uncomment for the field instances
//                            [
//                                'label' => Icon::show('screenshot', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]), 
//                                'url' => ['/radiometric-target/index']
//                            ],
//                            //\SILEX:info
                            //SILEX:info
                            //we have stop maintaining this functionnality for now. 
                            //Uncomment the following block to allow user to download the 4P acquisition session file
//                            Html::tag('li','',['class' => 'divider']),
//                            [
//                                'label' => Yii::t('app', 'Acquisition session template'),
//                                'items' => [
//                                    [
//                                        'label' => Icon::show('file-excel-o', [], Icon::FA). " " . Yii::t('app', "UAV"), 
//                                        'url' => ['/acquisition-session-metadata-file/generate-uav-metadata-file']
//                                    ],
//                                    [
//                                        'label' => Icon::show('file-excel-o', [], Icon::FA). " " . Yii::t('app', "Phenomobile"), 
//                                        'url' => ['/acquisition-session-metadata-file/generate-field-robot-metadata-file']
//                                    ],
//                                ]
//                            ]
                            //\SILEX:info
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Tools'),
                        'items' => [
                                [
                                    'label' => Icon::show('th', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]), 
                                    'url' => ['/group/index']
                                ],
                                [
                                    'label' => Icon::show('user', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 
                                    'url' => ['/user/index']
                                ],
                                [
                                    'label' => Icon::show('book', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 
                                    'url' => ['/document/index']
                                ],
                                [
                                    'label' => Icon::show('fire', [], Icon::BSG) . " " . Yii::t('app', 'Web API'), 
                                    'url' => WS_PHIS_PATH_DOC
                                ],
                                [
                                    'label' => Icon::show('link', [], Icon::BSG) . " " . Yii::t('app', 'Documentation'), 
                                    'url' => "https://opensilex.github.io/phis-docs-community/"
                                ],
                                [
                                    'label' => Icon::show('paperclip', [], Icon::BSG) . " " . Yii::t('app', 'Vocabulary'), 
                                    'url' => ['/site/ontology']
                                ],
                            ]
                        ];
        
        $menuItems[] = [
                            'label' => Icon::show('log-out', [], Icon::BSG) . " " . Yii::t('app', 'Logout'). ' ('. Yii::$app->session['email']. ')', 
                            'url' => ['/site/disconnect']
                        ];
        //connected user
    } else { 
        $menuItems[] = ['label' => Yii::t('app', 'Experimental Organization'),
                        'items' => [
                            [
                                'label' => Icon::show('home', ['class' => 'fas'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Scientific frame} other{Scientific frames}}', ['n' => 2]), 
                                'url' => ['/infrastructure'],
                            ],
                            [
                                'label' => Icon::show('folder-open', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Project} other{Projects}}', ['n' => 2]), 
                                'url' => ['/project/index'],
                            ],
                            [
                                'label' => Icon::show('flask', [], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Experiment} other{Experiments}}', ['n' => 2]),
                                'url' => ['/experiment/index']
                            ],
                            [
                                'label' => Icon::show('leaf', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]),
                                'url' => ['/scientific-object/index']
                            ],
                            [
                                'label' => Icon::show('eye-open', [], Icon::BSG) . " " . Yii::t('app', 'Variables'), 
                                'url' => ['/variable/index']
                            ],
                            [
                                'label' => Icon::show('wheat', ['class' => 'icofont-lg'], Icon::ICF) . " " . Yii::t('app', 'Species'), 
                                'url' => ['/species/index']
                            ],
                            [
                                'label' => Icon::show('flag', [], Icon::FA) . " " . Yii::t('app', 'Events'), 
                                'url' => ['/event/index']
                            ],
                            [
                                'label' => Icon::show('comment', [], Icon::FA) . " " . Yii::t('app', 'Annotations'), 
                                'url' => ['/annotation/index']
                            ],  
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Device'),
                        'items' => [
                            [
                                'label' => Icon::show('camera', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]), 
                                'url' => ['/sensor/index']
                            ],
                            [
                                'label' => Icon::show('bullhorn', ['class' => 'fa-large'], Icon::FA) . " " . Yii::t('app', '{n, plural, =1{Actuator} other{Actuators}}', ['n' => 2]), 
                                'url' => ['/actuator/index']
                            ],
                            [
                                'label' => Icon::show('blackboard', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]), 
                                'url' => ['/vector/index']
                            ],
                            //SILEX:info
                            //uncomment for the field instances
//                            [
//                                'label' => Icon::show('screenshot', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Radiometric Target} other{Radiometric Targets}}', ['n' => 2]), 
//                                'url' => ['/radiometric-target/index']
//                            ],
                            //\SILEX:info
                        ]];
        $menuItems[] = ['label' => Yii::t('app', 'Tools'),
                        'items' => [
                                [
                                    'label' => Icon::show('th', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Group} other{Groups}}', ['n' => 2]), 
                                    'url' => ['/group/index']
                                ],
                                [
                                    'label' => Icon::show('user', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Person} other{Persons}}', ['n' => 2]), 
                                    'url' => ['/user/index']
                                ],
                                [
                                    'label' => Icon::show('book', [], Icon::BSG) . " " . Yii::t('app', '{n, plural, =1{Document} other{Documents}}', ['n' => 2]), 
                                    'url' => ['/document/index']
                                ],
                                [
                                    'label' => Icon::show('fire', [], Icon::BSG) . " " . Yii::t('app', 'Web API'), 
                                    'url' => WS_PHIS_PATH_DOC
                                ],
                                [
                                    'label' => Icon::show('link', [], Icon::BSG) . " " . Yii::t('app', 'Documentation'), 
                                    'url' => "https://opensilex.github.io/phis-docs-community/"
                                ],
                            ]   
                        ];
        
        $menuItems[] = [
                            'label' => Icon::show('log-out', [], Icon::BSG) . " " . Yii::t('app', 'Logout'). ' ('. Yii::$app->session['email']. ')', 
                            'url' => ['/site/disconnect']
                        ];
    }
    
    echo NavX::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => $menuItems,
        'activateParents' => true,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        
        <div class="pull-right">
            <?php
                $urlFlag = \config::path()['basePath'] . '/images/icons/flags/';
                $urlLangage = \config::path()['baseIndexPath'] . '?r=site%2Flanguage&flag=';
            ?> 
            <?= Html::a('<img title="english" alt="EN" src="' . $urlFlag . 'drapeau-rond-en.png">', ['site/language', 'language' => 'en'], []) ?>
            <?= Html::a('<img title="french" alt="FR" src="' . $urlFlag . 'drapeau-rond-fr.png">', ['site/language', 'language' => 'fr'], []) ?>    
        </div>
        <div class="clearfix"></div>
        
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= $footerCopyrightWebappName; ?> v.3.2 - May 15, 2019 ; Software is licensed under AGPL-3.0 and data under CC BY-NC-SA 4.0</p>
    </div> 
</footer> 

 <!-- Script for handling user token expiration form -->
<script>
    $(document).ready(function() {
        /**
         * Function which display login form overlay when cookie is expires
         */
        var setOverlayTimer = function() {
            // Token timeout is determined with cookie value
            var tokenTimeout = Cookies.get("<?= WSConstants::TOKEN_COOKIE_TIMEOUT ?>");
            var delay = parseInt(tokenTimeout, 10) - Math.floor(Date.now() / 1000);

            if (delay >= 0) {
                // Define timeout
                setTimeout(function() {
                    $(".expiration-message").show();
                    $("#login-overlay").css({
                        opacity: 1,
                        visibility: "visible"
                    });
                }, delay * 1000);
            } else {
                // Redirect to login page if delay is already expired and not already on login page or on index
                var loginUrl = "<?= Yii::$app->urlManager->createUrl("site/login"); ?>";
                var controller = '<?= $this->context->module->controller->id ?>';
                var action = '<?= $this->context->module->controller->action->id ?>';
                var onIndex = (controller == 'site' && action == 'index');
                var onLogin = (controller == 'site' && action == 'login');
                
                if (!onLogin && !onIndex ) {
                    window.location.href = loginUrl;
                }
            }
        }

        // Inital overlay timer call (on page load)
        setOverlayTimer();

        var ajaxUrl = '<?php echo Url::to(['site/login-ajax']) ?>';

        // When login button is clicked in overlay
        $("#login-form-ajax").submit(function(event) {
            event.preventDefault();
            // Login threw ajax call
            $.post(ajaxUrl, $("#login-form-ajax").serialize(), function(data) {
                var jsonData = JSON.parse(data);

                if (jsonData.success) {
                    $("#login-overlay .json-error").html("");
                    if (jsonData.sameUser) {
                        // If success and same user
                        // hide the login form
                        $("#login-overlay").css({
                            opacity: 0,
                            visibility: "hidden"
                        });
                        // Refresh cookie value
                        Cookies.set("<?= WSConstants::TOKEN_COOKIE_TIMEOUT ?>", jsonData.tokenTimeout);
                        // Refresh login form overlay timer
                        setOverlayTimer();
                    } else {
                        // If success but with a new user, redirect to home to reload user rights
                        window.location.href = "<?= Yii::$app->getHomeUrl(); ?>";
                    }
                } else {
                    // In case of error display it
                    $("#login-overlay .json-error").html(jsonData.error);
                }
            })
        });
    });
</script> 

<?php if (Yii::$app->params['isDemo']): ?>
    <!-- Script for displaying demo login form -->
    <script>
        function openDemoLogin(event) {
            event.preventDefault();
            
            $("#login-overlay").css({
                opacity: 1,
                visibility: "visible"
            });
            
            return false;
        }
    </script>
    <!-- Demo Login form -->
    <div id="login-overlay" class="login-demo">
        <?php 
            $form = ActiveForm::begin([
                'id' => 'login-form-ajax',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "<div class=\"row\">{label}\n<div class=\"col-md-5\">{input}</div>\n<div class=\"col-md-5\">{error}</div></div>",
                    'labelOptions' => ['class' => 'col-md-2 control-label'],
                ],
            ]);

            $model = new \app\models\yiiModels\YiiTokenModel();
        ?>
            <h2><?= Yii::t('app/messages','Do you have an account or do you want to try OpenSILEX ?') ?></h2>

            <p class="expiration-message"><?= Yii::t('app/messages','Your session has expired') ?></p>
            
            <p style="color:red;"><b class="json-error"></b></p>
            <div class="fields">
                <?= $form->field($model, 'email')->hiddenInput(['value' => Yii::$app->params['demoLogin']])->label(false) ?>

                <?= $form->field($model, 'password')->hiddenInput(['value' => Yii::$app->params['demoPassword']])->label(false) ?>
            </div>
            <div class="row">
                <div class="col-md-6 login-button">
                    <?= Html::a('', ['/site/login'], ['class' => 'fa fa-users fa-4x', 'name' => 'login-button']) ?>
                    <div><?= Yii::t('app/messages','If you already have an account') ?></div>
                </div>
                <div class="col-md-6 login-button">
                    <?= Html::submitButton('', ['class' => 'fa fa-user-secret fa-4x', 'name' => 'login-button']) ?>
                    <div><?= Yii::t('app/messages','If you want to try OpenSILEX as guest') ?></div>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>    
<?php else: ?>
    <!-- Login form to allow user to reconnect without loosing work when token expires -->
    <div id="login-overlay">
        <?php 
            $form = ActiveForm::begin([
                'id' => 'login-form-ajax',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "<div class=\"row\">{label}\n<div class=\"col-md-5\">{input}</div>\n<div class=\"col-md-5\">{error}</div></div>",
                    'labelOptions' => ['class' => 'col-md-2 control-label'],
                ],
            ]);

            $model = new \app\models\yiiModels\YiiTokenModel();
        ?>
            <h2><?= Yii::t('app/messages','Your session has expired') ?></h2>

            <p>
                <?= Yii::t('app/messages','Please sign-in again:') ?>
            </p>

            <p style="color:red;"><b class="json-error"></b></p>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <div class="row">
                <div class="col-md-offset-2 col-md-10 login-button">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
<?php endif; ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
