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
                'blog/comments' => 'blog/comment/index',
                'blog/comment/<id:\d+>/<_a:[\w\-]+>' => 'blog/comment/<_a>',
                'blog/comment/<_a:[\w\-]+>' => 'blog/comment/<_a>',

                'blog/categories' => 'blog/category/index',
                'blog/category/<id:\d+>/<_a:[\w\-]+>' => 'blog/category/<_a>',
                'blog/category/<_a:[\w\-]+>' => 'blog/category/<_a>',

                'blog/posts' => 'blog/post/index',
                'blog/post/<id:\d+>/<_a:[\w\-]+>' => 'blog/post/<_a>',
                'blog/post/<_a:[\w\-]+>' => 'blog/post/<_a>',

                'blog/tags' => 'blog/tag/index',
                'blog/tags/<id:\d+>/<_a:[\w\-]+>' => 'blog/tag/<_a>',
                'blog/tags/<_a:[\w\-]+>' => 'blog/tag/<_a>',


                'blog' => 'blog/default/index',
                'blog/elfinder/<_a:[\w\-]+>' => 'blog/elfinder/<_a>',
                'blog/tag/<tag:[\w\-]+>' => 'blog/default/tag',
                'blog/<category:[\w_\/-]+>' => 'blog/default/category',
                'blog/<category:[\w_\/-]+>/<post:[\w_\/-]+><prefix:.html+>' => 'blog/default/post',

                'blog/<post:[\w_\/-]+><prefix:.html+>' => 'blog/default/post',
                'blog/<id:\d+>/<_a:[\w\-]+>' => 'blog/default/<_a>',
                'blog/<_a:[\w\-]+>' => 'blog/default/<_a>'
            ]
        );
    }
}
