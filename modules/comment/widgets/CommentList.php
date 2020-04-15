<?php

namespace modules\comment\widgets;

use Yii;
use modules\comment\models\Comment;
use yii\base\Widget;
use yii\base\Model;
use yii\base\InvalidConfigException;

/**
 * Class CommentList
 * @package modules\comment\widgets
 */
class CommentList extends Widget
{
    public $status;
    public $model;

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
            return $this->render('list', [
                'model' => $this->prepareModel()
            ]);
        }
    }

    /**
     * @return Comment
     */
    protected function prepareModel()
    {
        $model = new Comment();
        $model->entity = get_class($this->model);
        $model->entity_id = $this->model->id;
        return $model;
    }
}
