<?php

namespace modules\comment\widgets\form\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class FormAsset
 * @package modules\comment\widgets\form\assets
 */
class FormAsset extends AssetBundle
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
        $this->css = ['css/backend-comment-list.css'];
        $this->js = ['js/form.js'];
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
