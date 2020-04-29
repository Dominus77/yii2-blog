<?php

namespace api\modules\blog\v1\models;

use yii\helpers\ArrayHelper;
use modules\blog\models\Post as BasePost;

/**
 * Class Post
 * @package api\modules\blog\v1\models
 */
class Post extends BasePost
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
        return ['title', 'content'];
    }

    /**
     * /api/blog/v1/posts?expand=status
     * @return array
     */
    public function extraFields()
    {
        return ['status', 'created_at', 'updated_at'];
    }
}
