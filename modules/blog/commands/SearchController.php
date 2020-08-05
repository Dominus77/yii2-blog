<?php

namespace modules\blog\commands;

use Yii;
use yii\console\Controller;
use himiklab\yii2\search\Search;

/**
 * Class SearchController
 * @package modules\blog\commands
 */
class SearchController extends Controller
{
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
        /** @var Search $search */
        $search = Yii::$app->search;
        $search->index();
    }
}
