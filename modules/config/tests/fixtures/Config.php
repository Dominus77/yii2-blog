<?php

namespace modules\config\tests\fixtures;

use yii\test\ActiveFixture;
use modules\config\models\Config as BaseConfig;

/**
 * Class Config
 * @package modules\config\tests\fixtures
 */
class Config extends ActiveFixture
{
    public $modelClass = BaseConfig::class;
}
