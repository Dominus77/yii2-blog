<?php

namespace modules\blog\widgets\tag;

use Throwable;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Widget;
use modules\blog\models\Tag;
use modules\blog\Module;

/**
 * Class TagCloud
 * @package modules\blog\widgets\tag
 */
class TagCloud extends Widget
{
    public $status = true;
    public $title;
    public $panelOptions = ['class' => 'tag-cloud panel panel-default'];
    public $limit = 20;
    public $published = true;
    public $icon;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $icon = $this->icon ?: Html::tag('span', '', ['class' => 'glyphicon glyphicon-tags']) . ' ';
        $this->title = $this->title ?: $icon . Module::t('module', 'Tags');
        $this->id = $this->id ?: $this->getId();
    }

    /**
     * @return string|void
     * @throws Throwable
     */
    public function run()
    {
        if ($this->status === true) {
            $tags = Tag::model()->findTagWeights($this->limit, $this->published);
            echo Html::beginTag('div', ArrayHelper::merge(['id' => $this->id], $this->panelOptions)) . PHP_EOL;
            echo Html::tag('div', $this->title . PHP_EOL, ['class' => 'panel-heading']) . PHP_EOL;
            echo Html::beginTag('div', ['class' => 'panel-body']) . PHP_EOL;
            foreach ($tags as $tag => $weight) {
                echo Html::a(Html::tag('span', $tag, ['style' => "font-size:{$weight}pt"]), ['default/tag', 'tag' => $tag], ['rel' => 'nofollow']) . ' ' . PHP_EOL;
            }
            echo Html::endTag('div') . PHP_EOL;
            echo Html::endTag('div') . PHP_EOL;
        }
    }
}
