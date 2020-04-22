<?php

namespace modules\comment\widgets\items\assets;

use yii\web\AssetBundle;

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
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
