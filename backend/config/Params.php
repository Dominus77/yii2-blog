<?php

namespace backend\config;

use yii\helpers\ArrayHelper;
use modules\config\params\Params as BaseParams;

/**
 * Class Params
 * @package backend\config
 */
class Params extends BaseParams
{
    /**
     * Global params to site
     * @return array
     */
    public static function findParams()
    {
        $params = parent::findParams();
        $params = ArrayHelper::merge($params, []);
        return $params;
    }

    /**
     * @return array
     */
    public static function getReplace()
    {
        $replace = parent::getReplace();
        $replace = ArrayHelper::merge($replace, []);
        return $replace;
    }
}
