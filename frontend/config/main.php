<?php

use yii\log\FileTarget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use modules\users\models\User;
use modules\users\behavior\LastVisitBehavior;
use modules\main\Bootstrap as MainBootstrap;
use modules\users\Bootstrap as UserBootstrap;
use modules\rbac\Bootstrap as RbacBootstrap;
use modules\blog\Bootstrap as BlogBootstrap;
use modules\comment\Bootstrap as CommentBootstrap;
use dominus77\maintenance\Maintenance;
use dominus77\maintenance\filters\URIFilter;
use dominus77\maintenance\filters\RoleFilter;
use dominus77\maintenance\states\FileState;
use dominus77\maintenance\interfaces\StateInterface;
use dominus77\maintenance\controllers\frontend\MaintenanceController;
use common\url\AppUrlManager;
use modules\rbac\models\Permission;
use modules\config\components\behaviors\ConfigBehavior;

$params = ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

/**
 * This CSS Themes Bootstrap
 * ------------
 * cerulean
 * cosmo
 * cyborg
 * darkly
 * default
 * flatly
 * journal
 * lumen
 * paper
 * readable
 * sandstone
 * simplex
 * slate
 * spacelab
 * superhero
 * united
 * yeti
 * ------------
 * @package /frontend/assets/bootstrap
 * @var string
 */
$css_theme = 'default';

return [
    'id' => 'app-frontend',
    'language' => 'ru', // en, ru
    'homeUrl' => '/',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        MainBootstrap::class,
        UserBootstrap::class,
        RbacBootstrap::class,
        Maintenance::class,
        BlogBootstrap::class,
        CommentBootstrap::class
    ],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'main/default/index',
    'container' => [
        'singletons' => [
            Maintenance::class => [
                'class' => Maintenance::class,
                'route' => 'maintenance/index',
                'filters' => [
                    [
                        'class' => URIFilter::class,
                        'uri' => [
                            'debug/default/view',
                            'debug/default/toolbar',
                            'users/default/login',
                            'users/default/logout',
                            'users/default/request-password-reset'
                        ]
                    ],
                    [
                        'class' => RoleFilter::class,
                        'roles' => [
                            Permission::PERMISSION_MAINTENANCE
                        ]
                    ]
                ],
            ],
            StateInterface::class => [
                'class' => FileState::class,
                'directory' => '@runtime'
            ]
        ]
    ],
    'controllerMap' => [
        'maintenance' => [
            'class' => MaintenanceController::class,
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => ''
        ],
        'assetManager' => [
            'bundles' => [
                BootstrapAsset::class => [
                    'sourcePath' => '@frontend/assets/bootstrap',
                    'css' => [
                        YII_ENV_DEV ? $css_theme . '/bootstrap.css' : $css_theme . '/bootstrap.min.css'
                    ]
                ]
            ]
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['/users/default/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning']
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'frontend/error'
        ],
        'urlManager' => [
            'class' => AppUrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => []
        ],
        'urlManagerFrontend' => [
            'class' => AppUrlManager::class,
            'baseUrl' => '',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'email-confirm' => 'users/default/email-confirm'
            ]
        ],
        'urlManagerBackend' => [
            'class' => AppUrlManager::class,
            'baseUrl' => '/admin',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => []
        ],
    ],
    'as afterAction' => [
        'class' => LastVisitBehavior::class
    ],
    'as beforeConfig' => [
        'class' => ConfigBehavior::class,
    ],
    'params' => $params
];
