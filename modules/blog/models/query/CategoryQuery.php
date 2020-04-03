<?php

namespace modules\blog\models\query;

use modules\blog\models\Category;
use yii\db\ActiveQuery;
use paulzi\nestedsets\NestedSetsQueryTrait;

/**
 * This is the ActiveQuery class for [[\modules\blog\models\Category]].
 *
 * @see \modules\blog\models\Category
 */
class CategoryQuery extends ActiveQuery
{
    use NestedSetsQueryTrait;

    /**
     * @param bool $published
     * @return CategoryQuery
     */
    public function published($published = true)
    {
        $status = $published === true ? Category::STATUS_PUBLISH : Category::STATUS_DRAFT;
        return $this->andWhere(['status' => $status]);
    }
}
