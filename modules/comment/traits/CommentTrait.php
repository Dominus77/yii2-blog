<?php

namespace modules\comment\traits;

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
        return $this->getComments()
            ->andWhere(['status' => Comment::STATUS_WAIT])
            ->count();
    }

    /**
     * @param string[] $options
     * @return string
     */
    public function getCommentsLabelWaitCount($options = ['class' => 'pull-right label label-warning'])
    {
        $count = $this->getCommentsWaitCount();
        $tagOptions = ArrayHelper::merge($options, ['title' => Module::t('module', 'Comments waiting moderation')]);
        return ($count > 0) ? Html::tag('span', $count, $tagOptions) : '';
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
        return $this->getComments()
            ->andWhere(['status' => Comment::STATUS_BLOCKED])
            ->count();
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
