<?php

namespace modules\test;

use Yii;
use yii\console\Application as ConsoleApplication;

/**
 * Class Module
 * @package modules\test
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'modules\test\controllers\frontend';

    /**
     * @var bool If the module is used for the admin panel.
     */
    public $isBackend;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\test\controllers\backend';
            $this->setViewPath('@modules/test/views/backend');
        } else {
            $this->setViewPath('@modules/test/views/frontend');
        }
        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\test\commands';
        }
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/test/' . $category, $message, $params, $language);
    }
}
