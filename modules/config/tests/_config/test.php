<?php
defined('YII_APP_BASE_PATH') || define('YII_APP_BASE_PATH', __DIR__ . '/../../../../');
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/common/config/test-local.php'),
    [
        'id' => 'module-config',
        'language' => 'en',
        'bootstrap' => [
            '\modules\config\Bootstrap',
        ],
        'modules' => [
            'config' => [
                'class' => 'modules\config\Module',
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
                'class' => 'modules\config\components\DConfig',
                'duration' => 3600, // Время для кэширования
            ],
            'cache' => [
                'class' => 'yii\caching\FileCache',
                'cachePath' => '@console/runtime/cache',
            ],
        ],
    ]
);