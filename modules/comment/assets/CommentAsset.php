<?php

namespace modules\comment\assets;

use Yii;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class CommentAsset
 * @package modules\comment\assets
 */
class CommentAsset extends AssetBundle
{
    /** @var string Url to Ajax action */
    public $url;
    /** @var integer Exclude node id */
    public $id;
    /** @var string */
    public $sourcePath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/src';
        $this->js = ['js/comment.js'];
        $this->registerScript();
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

    /**
     * Params
     * @return string
     */
    protected function getJsParams()
    {
        return Json::encode([
            'url' => $this->url,
            'id' => $this->id ?: 0
        ]);
    }

    /**
     * Register Js
     */
    protected function registerScript()
    {
        $view = $view = Yii::$app->getView();
        $script = "commentInit({$this->getJsParams()});";
        $view->registerJs($script);
    }
}
