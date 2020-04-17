<?php

namespace modules\comment\widgets\items\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class CommentListAsset
 * @package modules\comment\widgets\items\assets
 */
class CommentListAsset extends AssetBundle
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
    public $depends = [
        JqueryAsset::class,
    ];

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
