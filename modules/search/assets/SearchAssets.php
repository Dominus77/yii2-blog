<?php

namespace modules\search\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class SearchAssets
 * @package modules\search\assets
 */
class SearchAssets extends AssetBundle
{
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src';
        $this->css[] = 'css/search.css';
        $this->js[] = YII_DEBUG ? 'js/jquery.highlight-5.js' : 'js/jquery.highlight-5.closure.js';
    }

    /**
     * @var string[]
     */
    public $depends = [
        JqueryAsset::class
    ];
}