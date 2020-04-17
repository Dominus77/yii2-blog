<?php

namespace common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\caching\TagDependency;
use yii\web\Controller;

/**
 * Class DelCacheControllerBehavior
 * @package common\components\behaviors
 */
class DelCacheControllerBehavior extends Behavior
{
    /**
     * @var array
     */
    public $tags = [];
    /**
     * @var array
     */
    public $actions;

    /**
     * @return array
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'deleteCache',
        ];
    }

    /**
     * Delete cache
     */
    public function deleteCache()
    {
        /** @var Controller $owner */
        $owner = $this->owner;
        $action_name = $owner->action->id;
        if (!in_array($action_name, $this->actions, true)) {
            return;
        }
        if (!is_array($this->tags)) {
            $this->tags = [$this->tags];
        }
        $cache = Yii::$app->cache;
        TagDependency::invalidate($cache, $this->tags);
    }
}
