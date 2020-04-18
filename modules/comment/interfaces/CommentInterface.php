<?php

namespace modules\comment\interfaces;

use yii\db\ActiveQuery;

/**
 * Interface CommentInterface
 * @package modules\comment\interfaces
 */
interface CommentInterface
{
    /**
     * Relations Entity to Comment
     * @return ActiveQuery
     */
    public function getComments();

    /**
     * This entity count comments
     * @return int
     */
    public function getCommentsCount();
}
