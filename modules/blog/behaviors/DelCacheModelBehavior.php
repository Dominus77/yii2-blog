<?php

namespace modules\blog\behaviors;

use Yii;
use yii\base\Behavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

/**
 * Class DelCacheModelBehavior
 * @package modules\blog\behaviors
 */
class DelCacheModelBehavior extends Behavior
{
    public $tags = [];

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
     * Delete cache
     */
    public function deleteCache()
    {
        if (!is_array($this->tags)) {
            $this->tags = [$this->tags];
        }
        $cache = Yii::$app->cache;
        TagDependency::invalidate($cache, $this->tags);
    }
}
