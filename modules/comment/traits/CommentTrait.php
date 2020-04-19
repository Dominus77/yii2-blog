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
