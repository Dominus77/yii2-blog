<?php

namespace modules\comment\widgets\form;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Widget;
use modules\comment\models\Comment;

/**
 * Class CommentForm
 * @package modules\comment\widgets\form
 */
class CommentForm extends Widget
{
    public $status;
    public $model;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->status = $this->status === true ?: false;
        if (!$this->model instanceof Model) {
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
            return $this->render('form', [
                'entity' => $this->model,
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
        if (Yii::$app->user->isGuest) {
            $model->scenario = $model::SCENARIO_GUEST;
        }
        $model->entity = get_class($this->model);
        $model->entity_id = $this->model->id;
        if ($user = Yii::$app->user->identity) {
            $model->author = $user->id;
            $model->email = $user->email;
        }
        return $model;
    }
}
