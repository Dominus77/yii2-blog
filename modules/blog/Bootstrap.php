<?php

namespace modules\blog;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap
 * @package modules\blog
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/blog/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/blog/messages',
            'fileMap' => [
                'modules/blog/module' => 'module.php'
            ]
        ];

        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                'blog' => 'blog/default/index',
                'blog/<id:\d+>/<_a:[\w\-]+>' => 'blog/default/<_a>',
                'blog/<_a:[\w\-]+>' => 'blog/default/<_a>',
            ]
        );
    }
}
