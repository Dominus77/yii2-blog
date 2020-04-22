<?php

namespace modules\comment\traits;

use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use modules\comment\models\Comment;
use modules\comment\models\query\CommentQuery;
use yii\db\ActiveQuery;
use modules\comment\Module;

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
        $query = $this->getComments()->andWhere(['status' => Comment::STATUS_WAIT]);
        $dependency = new TagDependency(['tags' => [Comment::CACHE_TAG_COMMENTS, Comment::CACHE_TAG_COMMENTS_COUNT_WAIT]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->count();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @param string[] $options
     * @return string
     */
    public function getCommentsLabelWaitCount($options = [])
    {
        $count = $this->getCommentsWaitCount();
        Html::addCssClass($options, 'pull-right label label-warning');
        $tagOptions = ArrayHelper::merge($options, ['title' => Module::t('module', 'Comments waiting moderation')]);
        return ($count > 0) ? Html::tag('span', $count, $tagOptions) : '';
    }

    /**
     * Return count comments this status approved
     * @return int
     */
    public function getCommentsApprovedCount()
    {
        $query = $this->getComments()->andWhere(['status' => Comment::STATUS_APPROVED]);
        $dependency = new TagDependency(['tags' => [Comment::CACHE_TAG_COMMENTS, Comment::CACHE_TAG_COMMENTS_COUNT_APPROVED]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->count();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @param string[] $options
     * @return string
     */
    public function getCommentsLabelApprovedCount($options = ['class' => 'pull-right label label-success'])
    {
        $count = $this->getCommentsApprovedCount();
        $tagOptions = ArrayHelper::merge($options, ['title' => Module::t('module', 'Approved comments')]);
        return ($count > 0) ? Html::tag('span', $count, $tagOptions) : '';
    }

    /**
     * Return count comments this status blocked
     * @return int
     */
    public function getCommentsBlockedCount()
    {
        $query = $this->getComments()->andWhere(['status' => Comment::STATUS_BLOCKED]);
        $dependency = new TagDependency(['tags' => [Comment::CACHE_TAG_COMMENTS, Comment::CACHE_TAG_COMMENTS_COUNT_BLOCKED]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->count();
        }, self::CACHE_DURATION, $dependency);
    }

    public function getCommentsLabelBlockedCount($options = ['class' => 'pull-right label label-danger'])
    {
        $count = $this->getCommentsBlockedCount();
        $tagOptions = ArrayHelper::merge($options, ['title' => Module::t('module', 'Blocked comments')]);
        return ($count > 0) ? Html::tag('span', $count, $tagOptions) : '';
    }

    /**
     * Count this entity comments status wait
     * @return int
     */
    public static function getEntityCommentsWaitCount()
    {
        $query = self::getEntityAllCommentsQuery()->andWhere(['status' => Comment::STATUS_WAIT]);
        $dependency = new TagDependency(['tags' => [Comment::CACHE_TAG_COMMENTS, Comment::CACHE_TAG_COMMENTS_COUNT_ENTITY_WAIT]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->count();
        }, self::CACHE_DURATION, $dependency);
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
