<?php

use modules\config\components\DConfig;
use yii\db\Connection;
use yii\rbac\DbManager;
use yii\caching\FileCache;
use yii\helpers\ArrayHelper;
use modules\main\Module as MainModule;
use modules\users\Module as UserModule;
use modules\rbac\Module as RbacModule;
use modules\blog\Module as BlogModule;
use modules\comment\Module as CommentModule;
use dominus77\maintenance\interfaces\StateInterface;
use dominus77\maintenance\states\FileState;
use modules\search\components\Search;
use modules\search\Bootstrap as SearchBootstrap;
use modules\search\Module as SearchModule;
use modules\blog\models\Post;

$params = ArrayHelper::merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'name' => 'Yii2-blog',
    'timeZone' => 'Europe/Moscow',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset'
    ],
    'bootstrap' => [
        SearchBootstrap::class
    ],
    'container' => [
        'singletons' => [
            StateInterface::class => [
                'class' => FileState::class,
                'dateFormat' => 'd-m-Y H:i:s',
                'directory' => '@frontend/runtime'
            ]
        ]
    ],
    'modules' => [
        'main' => [
            'class' => MainModule::class
        ],
        'users' => [
            'class' => UserModule::class
        ],
        'rbac' => [
            'class' => RbacModule::class
        ],
        'blog' => [
            'class' => BlogModule::class
        ],
        'comment' => [
            'class' => CommentModule::class
        ],
        'search' => [
            'class' => SearchModule::class
        ]
    ],
    'components' => [
        'config' => [
            'class' => DConfig::class,
            'duration' => 3600,
        ],
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=yii2_advanced_start',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'tbl_',
            'enableSchemaCache' => true
        ],
        'authManager' => [
            'class' => DbManager::class
        ],
        'cache' => [
            'class' => FileCache::class,
            'cachePath' => '@frontend/runtime/cache'
        ],
        'mailer' => [
            'useFileTransport' => false
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'basePath' => '@app/web/assets'
        ],
        'search' => [
            'class' => Search::class,
            'indexDirectory' => '@frontend/runtime/search',
            'models' => [
                Post::class,
            ],
        ],
    ]
];
