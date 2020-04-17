<?php

namespace modules\comment\widgets\other\assets;

use yii\web\AssetBundle;

/**
 * Class LastCommentAsset
 * @package modules\comment\widgets\other\assets
 */
class LastCommentAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath;

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src';
        $this->css[] = 'css/last-comment.css';
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
