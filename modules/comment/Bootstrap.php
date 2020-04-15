<?php

namespace modules\comment;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap
 * @package modules\comment
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/comment/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/comment/messages',
            'fileMap' => [
                'modules/comment/module' => 'module.php'
            ]
        ];

        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                'comment' => 'comment/default/index',
                'comment/<id:\d+>/<_a:[\w\-]+>' => 'comment/default/<_a>',
                'comment/<_a:[\w\-]+>' => 'comment/default/<_a>',
            ]
        );
    }
}
