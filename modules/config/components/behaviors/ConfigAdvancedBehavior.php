<?php

namespace modules\config\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use modules\config\params\Params;

/**
 * Class ConfigAdvancedBehavior
 * @package modules\config\components\behaviors
 */
class ConfigAdvancedBehavior extends Behavior
{
    /**
     * @var Params
     */
    public $paramsClass = Params::class;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * Set config
     */
    public function beforeAction()
    {
        /** @var Application $app */
        $app = $this->owner;
        $this->processReplace($app);
    }

    public function setParams()
    {
        return true;
    }

    /**
     * Set params
     * @param Application $app
     */
    private function processReplace(Application $app)
    {
        /** @var object $application */
        $application = Yii::$app;
        $config = $application->config;
        $array = $config->getAll();
        $paramsClass = $this->paramsClass;
        $replace = $paramsClass::getReplace();
        foreach ($replace as $key => $value) {
            if (isset($app->{$key})) {
                if ($key === 'language' && YII_ENV_TEST) {
                    $app->{$key} = $app->language;
                } else {
                    $app->{$key} = ArrayHelper::getValue($array, $value);
                }
            }
            if (isset($app->params[$key])) {
                $app->params[$key] = ArrayHelper::getValue($array, $value);
            }
        }
    }
}
