<?php

namespace modules\test;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap
 * @package modules\test
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/test/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/test/messages',
            'fileMap' => [
                'modules/test/module' => 'module.php'
            ]
        ];

        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                'test' => 'test/default/index',
                'test/<id:\d+>/<_a:[\w\-]+>' => 'test/default/<_a>',
                'test/<_a:[\w\-]+>' => 'test/default/<_a>'
            ]
        );
    }
}
