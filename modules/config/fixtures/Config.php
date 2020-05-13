<?php

namespace modules\config\fixtures;

use yii\test\ActiveFixture;
use modules\config\models\Config as BaseConfig;

/**
 * Class Config
 * @package modules\config\fixtures
 */
class Config extends ActiveFixture
{
    public $modelClass = BaseConfig::class;
}
