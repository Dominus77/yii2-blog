<?php

namespace modules\blog\commands;

use yii\console\Controller;
use modules\search\traits\SearchTrait;

/**
 * Class SearchController
 * @package modules\blog\commands
 */
class SearchController extends Controller
{
    use SearchTrait;

    /**
     * Color
     * @var bool
     */
    public $color = true;

    /**
     * Console user actions
     */
    public function actionIndex()
    {
        echo 'yii blog/search/indexing' . PHP_EOL;
    }

    public function actionIndexing()
    {
        $this->searchIndexing();
    }
}
