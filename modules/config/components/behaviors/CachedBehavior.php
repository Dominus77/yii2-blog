<?php

namespace modules\config\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class CachedBehavior
 * @package modules\config\components\behaviors
 */
class CachedBehavior extends Behavior
{
    /**
     * @var array
     */
    public $cache_id = [];

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'deleteCache',
            ActiveRecord::EVENT_AFTER_UPDATE => 'deleteCache',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteCache',
        ];
    }

    /**
     * Удаление массива кэшированных элементов (виджеты, модели...)
     */
    public function deleteCache()
    {
        foreach ($this->cache_id as $id) {
            Yii::$app->cache->delete($id);
        }
    }
}
