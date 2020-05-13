<?php

namespace modules\config\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yii\web\Application;

/**
 * Class ConfigBehavior
 * @package modules\config\components\behaviors
 */
class ConfigBehavior extends Behavior
{
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
        $this->setParams($app);
    }

    /**
     * Set params
     * @param Application $app
     */
    private function setParams(Application $app)
    {
        $this->setSiteName($app);
        $this->setTimeZone($app);
        $this->setLanguage($app);
    }

    /**
     * Set site name
     * @param Application $app
     */
    private function setSiteName(Application $app)
    {
        if (isset($app->name)) {
            $app->name = Yii::$app->config->get('SITE_NAME');
        }
    }

    /**
     * Set timezone
     * @param Application $app
     */
    private function setTimeZone(Application $app)
    {
        if (isset($app->timeZone)) {
            $app->timeZone = Yii::$app->config->get('SITE_TIME_ZONE');
        }
    }

    /**
     * Set language
     * @param Application $app
     */
    private function setLanguage(Application $app)
    {
        if (isset($app->language) && !YII_ENV_TEST) {
            $app->language = Yii::$app->config->get('SITE_LANGUAGE');
        }
    }
}
