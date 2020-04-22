<?php

namespace modules\blog\widgets\grid\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class GridAsset
 * @package modules\blog\widgets\grid\assets
 */
class GridAsset extends AssetBundle
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
        $this->css = ['css/style.css'];
        $this->js = ['js/grid.js'];
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
