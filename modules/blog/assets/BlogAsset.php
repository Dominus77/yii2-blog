<?php

namespace modules\blog\assets;

use yii\web\AssetBundle;

/**
 * Class BlogAsset
 * @package modules\blog\assets
 */
class BlogAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src';
        $this->css = ['css/blog.css'];
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
