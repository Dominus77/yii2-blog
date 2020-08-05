<?php

namespace modules\search\traits;

use Yii;
use modules\search\components\Search;
use Exception;

/**
 * Trait SearchTrait
 *
 * @package modules\search\traits
 */
trait SearchTrait
{
    /**
     * @return bool
     */
    public function indexing()
    {
        /** @var Search $search */
        $search = Yii::$app->search;
        try {
            $search->index();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
