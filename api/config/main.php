<?php

use yii\caching\FileCache;
use yii\rest\UrlRule;
use yii\log\FileTarget;
use yii\web\JsonParser;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\User;
use modules\users\Bootstrap as UserBootstrap;
use api\modules\v1\Module;
use modules\blog\Bootstrap as BlogBootstrap;

$params = ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'language' => 'en', // en, ru
    'homeUrl' => '/api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        UserBootstrap::class,
        BlogBootstrap::class
    ],
    'modules' => [
        'v1' => [
            'class' => Module::class
        ]
    ],
    'components' => [
        'request' => [
            'baseUrl' => '/api',
            'parsers' => [
                'application/json' => JsonParser::class
            ]
        ],
        'user' => [
            'identityClass' => User::class,
            'enableSession' => false,
            'enableAutoLogin' => false,
            'loginUrl' => null
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'v1/user',
                        'v1/post'
                    ],
                    'except' => ['delete'],
                    'pluralize' => true
                ],

            ]
        ],
        'cache' => [
            'class' => FileCache::class
        ]
    ],
    'params' => $params
];
