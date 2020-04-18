<?php

namespace modules\blog\interfaces;

use yii\db\ActiveQuery;

/**
 * Interface CommentInterface
 * @package modules\blog\interfaces
 */
interface CommentInterface
{
    /**
     * @return ActiveQuery
     */
    public function getComments();
}
