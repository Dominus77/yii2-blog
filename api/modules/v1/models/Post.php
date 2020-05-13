<?php

namespace api\modules\v1\models;

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Linkable;
use modules\blog\models\Post as BasePost;

/**
 * Class Post
 * @package api\modules\v1\models
 */
class Post extends BasePost implements Linkable
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), []);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), []);
    }

    /**
     * /api/blog/v1/post
     * @return array
     */
    public function fields()
    {
        return ['id', 'title', 'content'];
    }

    /**
     * /api/blog/v1/posts?expand=status
     * @return array
     */
    public function extraFields()
    {
        return [
            'comments' => 'comments'
        ];
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return [
            'self' => Url::to(['post/view', 'id' => $this->id], true),
        ];
    }
}
