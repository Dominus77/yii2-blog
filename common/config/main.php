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
use yii\elasticsearch\Connection as ElasticsearchConnection;

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
        'elasticsearch' => [
            'class' => ElasticsearchConnection::class,
            'autodetectCluster' => false,
            'nodes' => [
                ['http_address' => '127.0.0.1:9200'],
                //настройте несколько хостов, если у вас есть кластер
            ],
            //'dslVersion' => 7
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
        ]
    ]
];
