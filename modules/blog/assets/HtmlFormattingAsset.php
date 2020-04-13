<?php

namespace modules\blog\assets;

use yii\web\AssetBundle;

/**
 * Class HtmlFormattingAsset
 * @package modules\blog\assets
 */
class HtmlFormattingAsset extends AssetBundle
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
        $min = YII_ENV_DEV ? '' : '.min';
        $this->js = [
            'js/html-formatting' . $min . '.js'
        ];
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV
    ];
}
