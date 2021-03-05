<?php
namespace common\helpers;

/**
 * This holds custom static functions for use within application
 *
 * Class CustomFunctions
 *
 * @property CustomFunctions $customFunctions
 */
class CustomFunctions
{

    /**
     * returns constant description
     * @return string|null
     */
    public static function getConstantDescription($class, $value)
    {
        $map = array_flip((new \ReflectionClass($class))->getConstants());
        $constantName = (array_key_exists($value, $map) ? $map[$value] : null);
        if ($constantName) {
            return constant("$class::".$constantName.'_DESCRIPTION');
        }
        return $constantName;
    }
}