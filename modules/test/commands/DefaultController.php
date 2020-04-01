<?php

namespace modules\test\commands;

use yii\console\Controller;

/**
 * Class DefaultController
 * @package modules\test\commands
 */
class DefaultController extends Controller
{
    /**
     * Console default actions
     * @inheritdoc
     */
    public function actionIndex()
    {
        echo 'php yii test/default' . PHP_EOL;
    }
}
