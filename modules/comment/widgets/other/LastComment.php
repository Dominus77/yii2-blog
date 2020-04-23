<?php

namespace modules\comment\widgets\other;

use Throwable;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use modules\comment\widgets\other\assets\LastCommentAsset;
use modules\comment\models\Comment;
use modules\comment\Module;

/**
 * Class LastComment
 * @package modules\comment\widgets\other
 */
class LastComment extends Widget
{
    /** @var bool */
    public $status = true;
    /** @var int */
    public $limit = 5;
    /** @var string */
    public $title;
    /** @var string */
    public $icon;
    /** @var array */
    public $panelOptions = ['class' => 'last-comments panel panel-default'];
    /** @var string Attribute title or name this entity */
    public $titleAttribute = 'title';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $icon = $this->icon ?: Html::tag('span', '', ['class' => 'glyphicon glyphicon-comment']) . ' ';
        $this->title = $this->title ?: $icon . Module::t('module', 'Last Comments');
        $this->id = $this->id ?: $this->getId();
    }

    /**
     * @return string|void
     * @throws Throwable
     */
    public function run()
    {
        if (($this->status === true) && ($comments = $this->getComments()) && $comments !== null) {
            $this->registerAssets();
            echo Html::beginTag('div', ArrayHelper::merge(['id' => $this->id], $this->panelOptions)) . PHP_EOL;
            echo Html::tag('div', $this->title . PHP_EOL, ['class' => 'panel-heading']) . PHP_EOL;
            echo Html::beginTag('div', ['class' => 'panel-body']) . PHP_EOL;
            echo Html::beginTag('ul') . PHP_EOL;
            foreach ($comments as $comment) {
                if (($entityData = $comment->getEntityData()) && $entityData !== null) {
                    echo $this->render('last-comment', [
                        'comment' => $comment,
                        'title' => $entityData->{$this->titleAttribute}
                    ]);
                }
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
    public function getComments()
    {
        return Comment::getLastComments($this->limit);
    }

    /**
     * Register Assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        LastCommentAsset::register($view);
    }
}
