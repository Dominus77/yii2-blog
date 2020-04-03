<?php

namespace modules\blog\models\query;

use modules\blog\models\Post;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\modules\blog\models\Post]].
 *
 * @see \modules\blog\models\Post
 */
class PostQuery extends ActiveQuery
{
    /**
     * @param bool $published
     * @return PostQuery
     */
    public function published($published = true)
    {
        $status = $published === true ? Post::STATUS_PUBLISH : Post::STATUS_DRAFT;
        return $this->andWhere(['status' => $status]);
    }
}
