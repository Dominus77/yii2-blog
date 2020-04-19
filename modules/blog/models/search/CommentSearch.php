<?php

namespace modules\blog\models\search;

use modules\comment\models\query\CommentQuery;
use modules\comment\models\search\CommentSearch as BaseCommentSearch;
use modules\blog\models\Post;

/**
 * Class CommentSearch
 * @package modules\blog\models\search
 */
class CommentSearch extends BaseCommentSearch
{
    /**
     * @return CommentQuery
     */
    protected function getQuery()
    {
        return Post::getEntityAllCommentsQuery()
            ->andWhere(['>', 'depth', 0]);
    }
}
