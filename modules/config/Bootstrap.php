<?php

namespace modules\config;

use Yii;

/**
 * Class Bootstrap
 * @package modules\config
 */
class Bootstrap
{
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/config/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@modules/config/messages',
            'fileMap' => [
                'modules/config/module' => 'module.php',
            ],
        ];

        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                'config' => 'config/default/update',
            ]
        );
    }
}
