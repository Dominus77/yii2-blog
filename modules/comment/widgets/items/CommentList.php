<?php

namespace modules\comment\widgets\items;

use yii\base\Widget;
use yii\base\Model;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;
use modules\comment\models\Comment;
use modules\comment\widgets\items\assets\CommentListAsset;
use modules\comment\Module;

/**
 * Class CommentList
 * @package modules\comment\widgets\items
 */
class CommentList extends Widget
{
    public $status;
    public $reply = true;
    public $model;
    public $depthStart = 0;
    public $tree = true;
    public $showAll = false;
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
                if ($this->count > 0 || $this->reply === true) {
                    $title = $icon . ' ' . Module::t('module', 'There {n, plural, =0{are no comments yet, yours will be the first} =1{is one comment} other{are # comments}}', ['n' => $this->count]);
                    $options = ['class' => 'title-comments'];
                } else {
                    $title = $icon . ' ' . Module::t('module', 'Commenting is disabled');
                    $options = ['class' => 'title-comments-off'];
                }
                echo Html::tag('h3', $title, $options) . PHP_EOL;
                foreach ($tree as $items) {
                    echo $items . PHP_EOL;
                }
            }
            if ($this->reply === true) {
                echo Html::button($icon . ' ' . Module::t('module', 'Comment this'), [
                    'class' => 'comment-button btn btn-info btn-sm',
                ]);
            }
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
     * @param $index
     * @return string
     */
    protected function getItem($data, $index)
    {
        return $this->render('item', [
            'model' => $data,
            'index' => $index + 1,
            'avatar' => $this->getAvatar(),
            'reply' => $this->reply
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
        $status = $this->showAll === false ? Comment::STATUS_APPROVED : false;
        $nodes = $model->getNodes($status);
        $this->count = count($nodes);
        return $nodes;
    }

    /**
     * Register resource this widget
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        CommentListAsset::register($view);
        $script = "
            $('#form-container').show();
            $('.comment-button').hide();
        ";
        $view->registerJs($script);
    }

    /**
     * Avatar
     * @return string
     */
    public function getAvatar()
    {
        return Url::to(['/comment/default/file', 'filename' => 'defaultAvatar.jpg']);
    }
}
