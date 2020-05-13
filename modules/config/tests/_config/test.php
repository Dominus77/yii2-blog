<?php

use yii\caching\FileCache;
use modules\config\components\DConfig;
use modules\config\Module;
use modules\config\Bootstrap;

defined('YII_APP_BASE_PATH') || define('YII_APP_BASE_PATH', __DIR__ . '/../../../../');
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/common/config/test-local.php'),
    [
        'id' => 'module-config-test',
        'language' => 'en',
        'bootstrap' => [
            Bootstrap::class,
        ],
        'modules' => [
            'config' => [
                'class' => Module::class,
                'params' => [
                    'accessRoles' => ['?'],
                ],
            ],
        ],
        'components' => [
            'request' => [
                'csrfParam' => '_csrf-frontend-test',
                'enableCsrfValidation' => false,
            ],
            'config' => [
                'class' => DConfig::class,
                'duration' => 3600, // Время для кэширования
            ],
            'cache' => [
                'class' => FileCache::class,
                'cachePath' => '@console/runtime/cache',
            ],
        ],
    ]
);
