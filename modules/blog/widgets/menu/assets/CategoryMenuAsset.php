<?php

namespace modules\blog\widgets\menu\assets;

use yii\web\AssetBundle;

/**
 * Class CategoryMenuAsset
 * @package modules\blog\widgets\menu\assets
 */
class CategoryMenuAsset extends AssetBundle
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
        $this->css[] = 'css/menu.css';
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
