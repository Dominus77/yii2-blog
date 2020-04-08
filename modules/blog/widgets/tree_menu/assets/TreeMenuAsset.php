<?php

namespace modules\blog\widgets\tree_menu\assets;

use yii\web\AssetBundle;
use frontend\assets\AppAsset;

/**
 * Class TreeMenuAsset
 * @package modules\blog\widgets\tree_menu\assets
 */
class TreeMenuAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath;
    /**
     * @var array
     */
    public $css = [
        'style.css'
    ];
    /**
     * @var array
     */
    public $js = [
        'script.js'
    ];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src/tree_menu';
    }

    /**
     * @var array
     */
    public $depends = [
        AppAsset::class,
    ];
}
