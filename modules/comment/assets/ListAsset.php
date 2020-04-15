<?php

namespace modules\comment\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class ListAsset
 * @package modules\comment\assets
 */
class ListAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath;

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src';
        $this->css[] = 'css/comment-list.css';
        $this->js[] = 'js/script.js';
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class,
    ];
}
