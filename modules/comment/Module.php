<?php

namespace modules\comment;

use Yii;
use yii\console\Application as ConsoleApplication;

/**
 * Class Module
 * @package modules\comment
 */
class Module extends \yii\base\Module
{
    /** @var string */
    public static $name = 'comment';
    /** @var array */
    public $sizes = [10 => 10, 15 => 15, 20 => 20, 25 => 25, 50 => 50, 100 => 100, 200 => 200];
    /** @var int */
    public $defaultPageSize = 25;

    /**
     * @var string
     */
    public $controllerNamespace = 'modules\comment\controllers\frontend';

    /**
     * @var bool Если модуль используется для админ-панели.
     */
    public $isBackend;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\comment\controllers\backend';
            $this->setViewPath('@modules/comment/views/backend');
        } else {
            $this->setViewPath('@modules/comment/views/frontend');
        }
        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\comment\commands';
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
        return Yii::t('modules/comment/' . $category, $message, $params, $language);
    }
}
