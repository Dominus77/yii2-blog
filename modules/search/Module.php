<?php

namespace modules\search;

use Yii;
use yii\console\Application as ConsoleApplication;

/**
 * Class Module
 * @package modules\search
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'modules\search\controllers\frontend';

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
            $this->controllerNamespace = 'modules\search\controllers\backend';
            $this->setViewPath('@modules/search/views/backend');
        } else {
            $this->setViewPath('@modules/search/views/frontend');
        }
        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\search\commands';
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
        return Yii::t('modules/search/' . $category, $message, $params, $language);
    }
}
