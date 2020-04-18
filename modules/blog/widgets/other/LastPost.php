<?php

namespace modules\blog\widgets\other;

use Throwable;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Widget;
use modules\blog\models\Post;
use modules\blog\Module;
use modules\blog\widgets\other\assets\LastPostAsset;

/**
 * Class LastPost
 * @package modules\blog\widgets\other
 */
class LastPost extends Widget
{
    public $status = true;
    public $title;
    public $panelOptions = ['class' => 'last-post panel panel-default'];
    public $limit = 20;
    public $published = true;
    public $icon;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $icon = $this->icon ?: Html::tag('span', '', ['class' => 'glyphicon glyphicon-list']) . ' ';
        $this->title = $this->title ?: $icon . Module::t('module', 'Last Posts');
        $this->id = $this->id ?: $this->getId();
    }

    /**
     * @return string|void
     * @throws Throwable
     */
    public function run()
    {
        if ($this->status === true) {
            $this->registerAssets();
            $posts = $this->getData();
            echo Html::beginTag('div', ArrayHelper::merge(['id' => $this->id], $this->panelOptions)) . PHP_EOL;
            echo Html::tag('div', $this->title . PHP_EOL, ['class' => 'panel-heading']) . PHP_EOL;
            echo Html::beginTag('div', ['class' => 'panel-body']) . PHP_EOL;
            echo Html::beginTag('ul') . PHP_EOL;
            foreach ($posts as $post) {
                echo $this->render('last-post', [
                    'post' => $post
                ]);
            }
            echo Html::endTag('ul') . PHP_EOL;
            echo Html::endTag('div') . PHP_EOL;
            echo Html::endTag('div') . PHP_EOL;
        }
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    protected function getData()
    {
        return Post::model()->findLastPost($this->limit, $this->published);
    }

    /**
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        LastPostAsset::register($view);
    }
}
