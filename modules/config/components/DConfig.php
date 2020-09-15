<?php

namespace modules\config\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use modules\config\models\Config;
use modules\config\params\ConfigParams;
use modules\config\Module;
use yii\db\StaleObjectException;

/**
 * Class DConfig
 * @package modules\config\components
 */
class DConfig extends Component
{
    /**
     * @var int
     */
    public $duration = 0;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var  yii\caching\Cache
     */
    private $_cache;

    /**
     * @var null|mixed
     */
    private $_dependency;

    /**
     * @var string
     */
    private $_key;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_key = Config::CACHE_KEY;
        $this->_cache = Yii::$app->cache;
        $this->getData();
        parent::init();
    }

    /**
     * Get data
     */
    private function getData()
    {
        $items = $this->_cache->getOrSet($this->_key, function () {
            return $this->calculateSomething();
        });
        foreach ($items as $item) {
            if ($item->param) {
                $this->data[$item->param] = $item->value === '' ? $item->default : $item->value;
            }
        }
    }

    /**
     * @return Config[]
     */
    private function calculateSomething()
    {
        $data = Config::find()->all();
        $this->_cache->set($this->_key, $data, $this->duration, $this->_dependency);
        return $data;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws InvalidConfigException
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        throw new InvalidConfigException(Module::t('module', 'Undefined parameter {:Key}', [':Key' => $key]));
    }

    /**
     * @param string $key
     * @param int|string $value
     * @throws InvalidConfigException
     */
    public function set($key, $value)
    {
        $model = Config::findOne(['param' => $key]);
        if (!$model) {
            throw new InvalidConfigException(Module::t('module', 'Undefined parameter {:Key}', [':Key' => $key]));
        }

        $model->value = $value;

        if ($model->save()) {
            $this->data[$key] = $value;
            $this->clearCache($key);
        }
    }

    /**
     * @param array $params
     */
    public function add($params = [])
    {
        if (isset($params[0]) && is_array($params[0])) {
            foreach ($params as $item) {
                $this->createParameter($item);
            }
        } elseif ($params) {
            $this->createParameter($params);
        }
    }

    /**
     * @param string|array $key
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function delete($key)
    {
        if (is_array($key)) {
            foreach ($key as $item) {
                $this->removeParameter($item);
            }
        } elseif ($key) {
            $this->removeParameter($key);
        }
    }

    /**
     * @param array $param
     */
    protected function createParameter($param = [])
    {
        if (!empty($param['param'])) {
            $model = Config::findOne(['param' => $param['param']]);
            if ($model === null) {
                $model = new Config();
            }

            $model->param = $param['param'];
            $model->label = isset($param['label']) ? $param['label'] : $param['param'];
            $model->value = isset($param['value']) ? $param['value'] : '';
            $model->default = isset($param['default']) ? $param['default'] : '';
            $model->type = isset($param['type']) ? $param['type'] : ConfigParams::FIELD_TYPE_STRING;

            $model->save();
        }
    }

    /**
     * @param string $key
     * @throws \Throwable
     * @throws StaleObjectException
     */
    protected function removeParameter($key)
    {
        if (!empty($key)) {
            $model = Config::findOne(['param' => $key]);
            if ($model && $model->delete()) {
                $this->clearCache($key);
            }
        }
    }

    /**
     * Clear cache
     * @param $key
     */
    protected function clearCache($key)
    {
        $this->_cache->delete($key);
    }
}
