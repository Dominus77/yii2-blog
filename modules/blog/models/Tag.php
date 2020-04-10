<?php

namespace modules\blog\models;

use Throwable;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use modules\blog\behaviors\DelCacheModelBehavior;
use modules\blog\models\query\TagQuery;
use modules\blog\Module;

/**
 * This is the model class for table "{{%blog_tags}}".
 *
 * @property int $id ID
 * @property string $title Title
 * @property int $frequency Frequency
 * @property int $created_at Created
 * @property int $updated_at Updated
 * @property int $status Status
 *
 * @property TagPost[] $tagPost
 * @property Post[] $posts
 */
class Tag extends BaseModel
{
    /**
     * Минимальный размер шрифта
     */
    const MIN_FONT_SIZE = 8;
    /**
     * Максимальный размер шрифта
     */
    const MAX_FONT_SIZE = 18;

    const CACHE_DURATION = 0;
    const CACHE_TAG_TAG_CLOUD = 'tag-cloud';
    const CACHE_TAG_TAGS = 'tags';
    const CACHE_TAG_TAG_POSTS = 'tag-posts';

    /**
     * @param string $className
     * @return Tag
     */
    public static function model($className = __CLASS__)
    {
        return new $className;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_tags}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::class
            ],
            'delCacheModelBehavior' => [
                'class' => DelCacheModelBehavior::class,
                'tags' => [self::CACHE_TAG_TAGS, self::CACHE_TAG_TAG_CLOUD]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['frequency', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_PUBLISH],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'frequency' => Module::t('module', 'Frequency'),
            'created_at' => Module::t('module', 'Created'),
            'updated_at' => Module::t('module', 'Updated'),
            'status' => Module::t('module', 'Status')
        ];
    }

    /**
     * {@inheritdoc}
     * @return TagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagQuery(static::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getTagPost()
    {
        return $this->hasMany(TagPost::class, ['tag_id' => 'id']);
    }

    public static function findAllByName($query)
    {
        return self::find()->where(['title' => $query])->all();
    }

    /**
     * Posts to tag
     * @return ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['id' => 'post_id'])->via('tagPost');
    }

    /**
     * Posts to tags
     * @param bool $published
     * @return mixed
     * @throws Throwable
     */
    public function getTagPosts($published = false)
    {
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_TAGS, self::CACHE_TAG_TAG_POSTS]]);
        return self::getDb()->cache(function () use ($published) {
            $query = $this->getPosts();
            if ($published === true) {
                $query->published();
            }
            return $query->all();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * Posts data provider
     * @return mixed
     * @throws Throwable
     */
    public function getPostsDataProvider()
    {
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_TAGS, self::CACHE_TAG_TAG_POSTS]]);
        $query = $this->getPosts()->published();
        return self::getDb()->cache(static function () use ($query) {
            return new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => Post::PAGE_SIZE,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'created_at' => SORT_DESC,
                        'sort' => SORT_ASC,
                    ]
                ]
            ]);
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * All posts to tag
     * @param bool $published is true, return posts to status publish
     * @return int
     * @throws Throwable
     */
    public function getCountToPosts($published = false)
    {
        return count($this->getTagPosts($published));
    }

    /**
     * Возвращает теги вместе с их весом
     * @param int $limit число возвращаемых тегов
     * @param bool $published если true то только для тегов имеющих статус "опубликовано"
     * @return array вес с индексом равным имени тега
     * @throws Throwable
     */
    public function findTagWeights($limit = 20, $published = true)
    {
        $tags = [];
        $query = self::find();
        if ($published === true) {
            $query->published();
        }
        $query1 = clone($query);
        /** @var Tag $model */
        $query->limit($limit);

        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_TAG_CLOUD]]);
        $models = self::getDb()->cache(static function () use ($query) {
            return $query->all();
        }, self::CACHE_DURATION, $dependency);

        $sizeRange = self::MAX_FONT_SIZE - self::MIN_FONT_SIZE;

        $minCount = self::getDb()->cache(static function () use ($query1) {
            return log($query1->min('frequency') + 1);
        }, self::CACHE_DURATION, $dependency);
        $maxCount = self::getDb()->cache(static function () use ($query1) {
            return log($query1->max('frequency') + 1);
        }, self::CACHE_DURATION, $dependency);

        $countRange = ($maxCount - $minCount) ?: 1;

        foreach ($models as $model) {
            $tags[$model->title] = round(self::MIN_FONT_SIZE + (log($model->frequency + 1) - $minCount) * ($sizeRange / $countRange));
        }
        return $tags;
    }
}
