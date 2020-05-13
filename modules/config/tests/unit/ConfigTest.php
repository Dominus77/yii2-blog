<?php

namespace modules\config\tests\unit;

use Yii;
use yii\base\InvalidConfigException;
use modules\config\params\Params;
use modules\config\models\Config;
use modules\config\params\ConfigParams;

/**
 * Class ConfigTest
 * @package modules\config\tests\unit
 */
class ConfigTest extends \Codeception\Test\Unit
{
    /**
     * @var \modules\config\tests\UnitTester
     */
    protected $tester;

    public function testConfigParams()
    {
        $this->assertTrue(is_array(ConfigParams::findParams()));
        $this->assertTrue(is_array(ConfigParams::getReplace()));
    }

    /**
     * Check attributeLabels in the model Config
     */
    public function testCheckAttributeLabels()
    {
        $params = Params::findParams();
        $model = new Config();
        foreach ($params[0] as $key => $attribute) {
            $this->assertArrayHasKey($key, $model->attributeLabels());
        }
    }

    /**
     * This get default value
     */
    public function testGetDefaultValue()
    {
        $model = new Config([
            'param' => 'TEST_NAME',
            'label' => 'Test Name',
            'value' => '',
            'type' => ConfigParams::FIELD_TYPE_STRING,
            'default' => 'Tester',
        ]);
        $this->assertTrue($model->save());

        $app = Yii::$app;
        $config = $app->config;
        $name = $config->get('TEST_NAME');
        $this->assertEquals($name, 'Tester');
    }

    /**
     * This get value
     */
    public function testGetValue()
    {
        $model = new Config([
            'param' => 'TEST_NAME',
            'label' => 'Test Name',
            'value' => 'Ym Tester',
            'type' => ConfigParams::FIELD_TYPE_STRING,
            'default' => 'Tester',
        ]);
        $this->assertTrue($model->save());

        $app = Yii::$app;
        $config = $app->config;
        $name = $config->get('TEST_NAME');
        $this->assertEquals($name, 'Ym Tester');
    }

    /**
     * This get not params
     */
    public function testGetValueNotParam()
    {
        $this->tester->expectException(new InvalidConfigException('Undefined parameter NONE_PARAM'), function () {
            $this->getNotParam();
        });
    }

    /**
     * @return InvalidConfigException
     */
    protected function getNotParam()
    {
        $model = new Config([
            'param' => 'TEST_NAME',
            'label' => 'Test Name',
            'value' => '',
            'type' => ConfigParams::FIELD_TYPE_STRING,
            'default' => 'Tester',
        ]);
        $this->assertTrue($model->save());

        $app = Yii::$app;
        $config = $app->config;
        return $config->get('NONE_PARAM');
    }

    /**
     * This set value success
     */
    public function testSetValueSuccess()
    {
        $model = new Config([
            'param' => 'TEST_NAME',
            'label' => 'Test Name',
            'value' => '',
            'type' => ConfigParams::FIELD_TYPE_STRING,
            'default' => 'Tester',
        ]);
        $this->assertTrue($model->save());

        $app = Yii::$app;
        $config = $app->config;
        $name = $config->get('TEST_NAME');
        $this->assertEquals($name, 'Tester');

        $config->set('TEST_NAME', 'Ym Tester');
        $name = $config->get('TEST_NAME');
        $this->assertEquals($name, 'Ym Tester');
    }
}
