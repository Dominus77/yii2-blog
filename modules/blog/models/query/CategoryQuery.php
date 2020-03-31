<?php

namespace modules\blog\models\query;

use yii\db\ActiveQuery;
use creocoder\nestedsets\NestedSetsQueryBehavior;

/**
 * This is the ActiveQuery class for [[\modules\blog\models\Category]].
 *
 * @see \modules\blog\models\Category
 */
class CategoryQuery extends ActiveQuery
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            NestedSetsQueryBehavior::class,
        ];
    }
}
