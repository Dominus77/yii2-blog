<?php

namespace modules\blog\traits;

use modules\blog\models\Category;
use Yii;
use yii\base\InvalidConfigException;
use modules\blog\Module;

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
     */
    public static function getCategoriesTree($excludeNodeId = null)
    {
        return Category::getTree($excludeNodeId);
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
        return $formatter->asDatetime($date, 'php:d-m-Y H:i:s');
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
}
