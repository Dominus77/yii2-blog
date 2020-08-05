<?php

namespace modules\search\traits;

use Yii;
use modules\search\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\search\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('search');
    }
}
