<?php

namespace modules\blog\traits;

use Yii;
use yii\base\InvalidConfigException;
use modules\blog\models\Category;
use modules\blog\Module;
use yii\helpers\StringHelper;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\blog\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule(Module::$name);
    }

    /**
     * Get a full tree as a list, except the node and its children
     * @param null $excludeNodeId
     * @return array
     * @throws \Throwable
     */
    public static function getCategoriesTree($excludeNodeId = null)
    {
        return Category::getFullTree($excludeNodeId);
    }

    /**
     * Format Date
     * @param integer $date
     * @return string
     * @throws InvalidConfigException
     */
    public static function getFormatData($date)
    {
        $formatter = Yii::$app->formatter;
        return $formatter->asDatetime($date, 'php:Y-m-d H:i:s');
    }

    /**
     * Sizes
     * @param array $sizes
     * @return array
     */
    public static function getSizes($sizes = [])
    {
        if (empty($sizes)) {
            /** @var Module $module */
            $module = Yii::$app->getModule(Module::$name);
            $sizes = $module->sizes;
        }
        return $sizes;
    }

    /**
     * Default Page Size
     * @param null|integer $defaultPageSize
     * @return int|null
     */
    public static function getDefaultPageSize($defaultPageSize = null)
    {
        if ($defaultPageSize === null) {
            /** @var Module $module */
            $module = Yii::$app->getModule(Module::$name);
            $defaultPageSize = $module->defaultPageSize;
        }
        return $defaultPageSize;
    }

    /**
     * @param $string
     * @param int $length
     * @param string $sufix
     * @return string
     */
    public static function truncate($string, $length = 30, $sufix = ' ...')
    {
        return StringHelper::truncate($string, $length, $sufix);
    }
}
