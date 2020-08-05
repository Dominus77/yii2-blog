<?php

namespace modules\search\commands;

use yii\console\Controller;

/**
 * Class DefaultController
 * @package modules\search\commands
 */
class DefaultController extends Controller
{
    /**
     * Console default actions
     * @inheritdoc
     */
    public function actionIndex()
    {
        echo 'php yii search/default' . PHP_EOL;
    }
}
