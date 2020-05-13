<?php

namespace modules\config\traits;

use Yii;
use modules\config\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\config\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('config');
    }
}
