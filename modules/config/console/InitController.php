<?php

namespace modules\config\console;

use Yii;
use yii\base\Action;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use modules\config\components\helpers\Console;
use modules\config\traits\ModuleTrait;
use modules\config\Module;
use modules\config\params\ConfigParams;

/**
 * Class InitController
 * @package modules\config\console
 */
class InitController extends Controller
{
    use ModuleTrait;

    /** @var ConfigParams $params */
    protected $params;

    /**
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     */
    public function beforeAction($action)
    {
        if (empty($this->getModule()->params['paramsClass'])) {
            throw new InvalidConfigException(Module::t('module', 'You must specify the params[Params::class] class in the module settings.'));
        }
        $this->params = $this->getModule()->params['paramsClass'];
        return parent::beforeAction($action);
    }

    /**
     * All Commands
     * @inheritdoc
     */
    public function actionIndex()
    {
        echo 'yii config/init/up' . PHP_EOL;
        echo 'yii config/init/down' . PHP_EOL;
        echo 'yii config/init/update' . PHP_EOL;
    }

    /**
     * Loading data config into the in database
     * @inheritdoc
     */
    public function actionUp()
    {
        $configParams = $this->params;
        /** @var object $app */
        $app = Yii::$app;
        $app->config->add($configParams::findParams());
        echo $this->log(true);
    }

    /**
     * Remove data config from the in database
     */
    public function actionDown()
    {
        $configParams = $this->params;
        $params = ArrayHelper::getColumn($configParams::findParams(), 'param');
        /** @var object $app */
        $app = Yii::$app;
        $app->config->delete($params);
        echo $this->log(true);
    }

    /**
     * Update data config from the in database
     */
    public function actionUpdate()
    {
        $configParams = $this->params;
        $params = ArrayHelper::getColumn($configParams::findParams(), 'param');
        /** @var object $app */
        $app = Yii::$app;
        $app->config->delete($params);
        $app->config->add($configParams::findParams());
        echo $this->log(true);
    }

    /**
     * @param bool|int $success
     */
    private function log($success = false)
    {
        if ($success === true || $success !== 0) {
            $this->stdout(Console::convertEncoding(Module::t('module', 'Success!')), Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr(Console::convertEncoding(Module::t('module', 'Error!')), Console::FG_RED, Console::BOLD);
        }
        echo PHP_EOL;
    }
}
