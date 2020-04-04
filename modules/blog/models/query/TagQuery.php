<?php

namespace modules\blog\models\query;

use modules\blog\models\Tag;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\modules\blog\models\Tag]].
 *
 * @see \modules\blog\models\Tag
 */
class TagQuery extends ActiveQuery
{
    /**
     * @param bool $published
     * @return TagQuery
     */
    public function published($published = true)
    {
        $status = $published === true ? Tag::STATUS_PUBLISH : Tag::STATUS_DRAFT;
        return $this->andWhere(['status' => $status]);
    }
}
