<?php

namespace modules\comment\traits;

use modules\comment\models\Comment;
use modules\comment\models\query\CommentQuery;
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
        return $this->hasMany(Comment::class, ['entity_id' => 'id'])
            ->andWhere(['entity' => __CLASS__]);
    }

    /**
     * This entity count comments
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->getComments()->count();
    }

    /**
     * Return count comments this status wait
     * @return int
     */
    public function getCommentsWaitCount()
    {
        return $this->getComments()
            ->andWhere(['status' => Comment::STATUS_WAIT])
            ->count();
    }

    /**
     * Return count comments this status approved
     * @return int
     */
    public function getCommentsApprovedCount()
    {
        return $this->getComments()
            ->andWhere(['status' => Comment::STATUS_APPROVED])
            ->count();
    }

    /**
     * Return count comments this status blocked
     * @return int
     */
    public function getCommentsBlockedCount()
    {
        return $this->getComments()
            ->andWhere(['status' => Comment::STATUS_BLOCKED])
            ->count();
    }

    /**
     * Count this entity comments status wait
     * @return int
     */
    public static function getEntityCommentsWaitCount()
    {
        return self::getEntityAllCommentsQuery()
            ->andWhere(['status' => Comment::STATUS_WAIT])
            ->count();
    }

    /**
     * @return CommentQuery
     */
    public static function getEntityAllCommentsQuery()
    {
        return Comment::find()
            ->where(['entity' => __CLASS__]);
    }
}
