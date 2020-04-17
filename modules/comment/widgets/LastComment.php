<?php

namespace modules\comment\widgets;

use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use modules\comment\models\Comment;
use modules\comment\Module;

/**
 * Class LastComment
 * @package modules\comment\widgets
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
     */
    public function run()
    {
        if (($this->status === true) && ($comments = $this->getComments()) && $comments !== null) {
            echo Html::beginTag('div', ArrayHelper::merge(['id' => $this->id], $this->panelOptions)) . PHP_EOL;
            echo Html::tag('div', $this->title . PHP_EOL, ['class' => 'panel-heading']) . PHP_EOL;
            echo Html::beginTag('div', ['class' => 'panel-body']) . PHP_EOL;
            echo $this->render('last-comment', [
                'comments' => $comments
            ]);
            echo Html::endTag('div') . PHP_EOL;
            echo Html::endTag('div') . PHP_EOL;
        }
    }

    /**
     * @return array|ActiveRecord[]
     */
    public function getComments()
    {
        return Comment::getLastComments($this->limit);
    }
}
