<?php

namespace modules\comment\traits;

use Yii;
use yii\base\InvalidConfigException;
use modules\comment\models\Comment;
use modules\comment\Module;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\blog\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule(Module::$name);
    }

    /**
     * Format Date
     * @param integer $date
     * @return string
     * @throws InvalidConfigException
     */
    public static function getFormatData($date)
    {
        $formatter = Yii::$app->formatter;
        return $formatter->asDatetime($date, 'php:Y-m-d H:i:s');
    }

    /**
     * Sizes
     * @param array $sizes
     * @return array
     */
    public static function getSizes($sizes = [])
    {
        if (empty($sizes)) {
            /** @var Module $module */
            $module = Yii::$app->getModule(Module::$name);
            $sizes = $module->sizes;
        }
        return $sizes;
    }

    /**
     * Default Page Size
     * @param null|integer $defaultPageSize
     * @return int|null
     */
    public static function getDefaultPageSize($defaultPageSize = null)
    {
        if ($defaultPageSize === null) {
            /** @var Module $module */
            $module = Yii::$app->getModule(Module::$name);
            $defaultPageSize = $module->defaultPageSize;
        }
        return $defaultPageSize;
    }

    /**
     * Get a full tree as a list, except the node and its children
     * @param null $excludeNodeId
     * @return array
     */
    public static function getCommentTree($excludeNodeId = null)
    {
        return Comment::getFullTree($excludeNodeId);
    }

    /**
     * Count wait status comments
     * @return int
     */
    public static function getCommentsWaitCount()
    {
        $query = self::find()->where(['status' => self::STATUS_WAIT]);
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_COMMENTS, self::CACHE_TAG_COMMENTS_COUNT_WAIT]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->count();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @param string[] $options
     * @return string
     */
    public static function getCommentsLabelWaitCount($options = [])
    {
        $count = self::getCommentsWaitCount();
        Html::addCssClass($options, 'pull-right label label-warning');
        $tagOptions = ArrayHelper::merge($options, ['title' => Module::t('module', 'Comments waiting moderation')]);
        return ($count > 0) ? Html::tag('span', $count, $tagOptions) : '';
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @return string containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function getGravatar($email = '', $s = '50', $d = 'mm', $r = 'g')
    {
        $data = ['email' => $email, 's' => $s, 'd' => $d, 'r' => $r];
        $key = 'gravatar_' . md5($email);
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_COMMENTS, self::CACHE_TAG_COMMENTS_AVATAR]]);
        $cache = Yii::$app->cache;
        return $cache->getOrSet($key, static function () use ($data) {
            $url = 'https://www.gravatar.com/avatar/';
            $url .= md5(strtolower(trim($data['email']))) . '?';
            $url .= http_build_query([
                's' => $data['s'],
                'd' => $data['d'],
                'r' => $data['r'],
            ]);
            return $url;
        }, self::CACHE_DURATION, $dependency);
    }
}
