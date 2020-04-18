<?php

namespace modules\comment\traits;

use modules\comment\models\Comment;
use yii\db\ActiveQuery;

/**
 * Trait CommentTrait
 * @package modules\comment\traits
 */
trait CommentTrait
{
    /**
     * Relations Entity to Comment
     * @return ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['entity_id' => 'id'])->andWhere(['entity' => __CLASS__]);
    }

    /**
     * This entity count comments
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->getComments()->count();
    }
}
