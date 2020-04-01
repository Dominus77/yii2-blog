<?php

namespace modules\test\traits;

use Yii;
use modules\test\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\test\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('test');
    }
}
