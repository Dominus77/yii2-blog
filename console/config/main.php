<?php

use yii\log\FileTarget;
use yii\console\controllers\MigrateController;
use yii\helpers\ArrayHelper;
use modules\rbac\Module as RbacModule;
use modules\main\Bootstrap as MainBootstrap;
use modules\users\Bootstrap as UserBootstrap;
use modules\rbac\Bootstrap as RbacBootstrap;
use modules\blog\Bootstrap as BlogBootstrap;
use modules\users\models\User;
use dominus77\maintenance\states\FileState;
use dominus77\maintenance\interfaces\StateInterface;
use dominus77\maintenance\BackendMaintenance;
use dominus77\maintenance\commands\MaintenanceController;
use modules\config\Bootstrap as ConfigBootstrap;
use modules\config\Module as ConfigModule;
use common\url\AppUrlManager;

$params = ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'language' => 'ru', // en, ru
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        ConfigBootstrap::class,
        MainBootstrap::class,
        UserBootstrap::class,
        RbacBootstrap::class,
        BackendMaintenance::class,
        BlogBootstrap::class
    ],
    'container' => [
        'singletons' => [
            StateInterface::class => [
                'class' => FileState::class,
                'directory' => '@frontend/runtime',
            ]
        ]
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'modules\users\migrations',
                'modules\rbac\migrations',
                'modules\config\migrations',
                'modules\blog\migrations',
                'modules\comment\migrations',
            ]
        ],
        'maintenance' => [
            'class' => MaintenanceController::class,
        ],
    ],
    'modules' => [
        'config' => [
            'class' => ConfigModule::class,
        ],
        'rbac' => [
            'class' => RbacModule::class,
            'params' => [
                'userClass' => User::class
            ]
        ]
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning']
                ]
            ]
        ],
        'urlManager' => [
            'class' => AppUrlManager::class,
            'baseUrl' => '/',
            'hostInfo' => $params['frontendUrl'], // set in common/config/params.php
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => []
        ],
    ],
    'params' => $params
];
