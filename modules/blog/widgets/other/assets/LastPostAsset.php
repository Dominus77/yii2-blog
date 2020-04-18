<?php


namespace modules\blog\widgets\other\assets;

use yii\web\AssetBundle;

/**
 * Class LastPostAsset
 * @package modules\blog\widgets\other\assets
 */
class LastPostAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src';
        $this->css[] = 'css/last-post.css';
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
