<?php

namespace modules\config\params;

use modules\config\components\interfaces\ConfigInterface;

/**
 * Class ConfigParams
 * @package modules\config\params
 */
class ConfigParams implements ConfigInterface
{
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_INTEGER = 'integer';
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_CHECKBOX = 'bool';

    /**
     * @return array
     */
    public static function findParams()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getReplace()
    {
        return [];
    }
}
