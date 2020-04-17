<?php

namespace modules\comment\widgets;

use yii\base\Widget;
use yii\base\Model;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use modules\comment\models\Comment;
use modules\comment\assets\ListAsset;
use modules\comment\Module;

/**
 * Class CommentList
 * @package modules\comment\widgets
 */
class CommentList extends Widget
{
    public $status;
    public $model;
    public $depthStart = 0;
    public $tree = true;
    private $assets;
    private $count = 0;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->status = $this->status ?: false;
        if ($this->status === true && !$this->model instanceof Model) {
            throw new InvalidConfigException('Модель не является экземпляром класса ' . Model::class);
        }
        if ($this->model === null) {
            $this->status = false;
        }
    }

    /**
     * @return string|void
     */
    public function run()
    {
        if ($this->status === true) {
            $this->registerAssets();
            echo Html::beginTag('div', ['id' => $this->id, 'class' => 'comments']) . PHP_EOL;
            $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-comment']);
            if (is_array($tree = $this->getRenderTree())) {
                $title = $icon . ' ' . Module::t('module', 'Comments ({:count})', [':count' => $this->count]);
                echo Html::tag('h3', $title, ['class' => 'title-comments']) . PHP_EOL;
                foreach ($tree as $items) {
                    echo $items . PHP_EOL;
                }
            }
            echo Html::button($icon . ' ' . Module::t('module', 'Comment this'), [
                'id' => 'comment-this-button',
                'class' => 'btn btn-info btn-sm',
                'style' => 'display:none;'
            ]);
            echo Html::endTag('div') . PHP_EOL;
        }
    }

    /**
     * Render List Tree
     * @return array
     */
    protected function getRenderTree()
    {
        $array = [];
        if ($query = $this->prepareModel()) {

            $depth = $this->depthStart;
            $i = 0;
            foreach ($query as $n => $items) {
                if ($items->depth === $depth) {
                    $array[] = $i ? Html::endTag('li') . PHP_EOL : '';
                } else if ($items->depth > $depth) {
                    $options = $n === 0 ? ['class' => 'comment-list'] : [];
                    $array[] = Html::beginTag('ul', $options) . PHP_EOL;
                } else {
                    $array[] = Html::endTag('li') . PHP_EOL;
                    for ($i = $depth - $items->depth; $i; $i--) {
                        $array[] = Html::endTag('ul') . PHP_EOL;
                        $array[] = Html::endTag('li') . PHP_EOL;
                    }
                }
                $array[] = Html::beginTag('li', [
                        'id' => 'comment-' . $items->id,
                        'class' => 'item_' . $n,
                        'data-id' => $items->id
                    ]) . PHP_EOL;
                $array[] = $this->getItem($items, $n) . PHP_EOL;
                $depth = $items->depth;
                $i++;
            }
            $correct = $this->depthStart > 1 ? 1 : 0;
            for ($i = $depth - $correct; $i; $i--) {
                $array[] = Html::endTag('li') . PHP_EOL;
                $array[] = Html::endTag('ul') . PHP_EOL;
            }
        }
        return $array;
    }

    /**
     * Render Item
     * @param $data
     * @param $key
     * @return string
     */
    private function getItem($data, $index)
    {
        return $this->render('item', [
            'model' => $data,
            'index' => $index + 1,
            'avatar' => $this->getAvatar()
        ]);
    }

    /**
     * Get prepare data tree
     * @return array|Comment[]|ActiveRecord[]
     */
    protected function prepareModel()
    {
        $model = new Comment();
        $model->entity = get_class($this->model);
        $model->entity_id = $this->model->id;
        $nodes = $model->getNodes();
        $this->count = count($nodes);
        return $nodes;
    }

    /**
     * Register resource this widget
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        $this->assets = ListAsset::register($view);
    }

    /**
     * Avatar
     * @return string
     */
    public function getAvatar()
    {
        return $this->assets->baseUrl . '/image/defaultAvatar.jpg';
    }
}
