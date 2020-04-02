<?php

namespace modules\blog\models\query;

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
}
