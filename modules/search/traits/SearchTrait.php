<?php

namespace modules\search\traits;

use Yii;
use yii\base\InvalidConfigException;
use modules\search\components\Search;

/**
 * Trait SearchTrait
 *
 * @package modules\search\traits
 */
trait SearchTrait
{
    /**
     * @throws InvalidConfigException
     */
    public function searchIndexing()
    {
        /** @var Search $search */
        $search = Yii::$app->search;
        $search->index();
    }
}
