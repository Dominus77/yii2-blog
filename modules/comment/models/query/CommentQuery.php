<?php

namespace modules\comment\models\query;

use modules\comment\models\Comment;
use paulzi\nestedsets\NestedSetsQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\modules\comment\models\Comment]].
 *
 * @see \modules\comment\models\Comment
 */
class CommentQuery extends ActiveQuery
{
    use NestedSetsQueryTrait;

    /**
     * @return CommentQuery
     */
    public function approved()
    {
        return $this->andWhere(['status' => Comment::STATUS_APPROVED]);
    }
}
