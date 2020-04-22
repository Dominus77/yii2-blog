<?php

namespace modules\blog\models;

use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii2tech\ar\position\PositionBehavior;
use dosamigos\taggable\Taggable;
use modules\blog\models\query\TagQuery;
use modules\users\models\UserProfile;
use modules\users\models\User;
use modules\blog\models\query\PostQuery;
use common\components\behaviors\DelCacheModelBehavior;
use modules\blog\Module;
use modules\comment\traits\CommentTrait;

/**
 * Class Post
 * @package modules\blog\models
 *
 * @property int $id ID
 * @property string $title Title
 * @property string $slug Alias
 * @property string $anons Anons
 * @property string $content Content
 * @property int $category_id Category
 * @property int $author_id Author
 * @property int $created_at Created
 * @property int $updated_at Updated
 * @property int $status Status
 * @property int $sort Position
 * @property int $is_comment Is Comment
 *
 * @property User $author
 * @property User $postAuthor
 * @property UserProfile $postAuthorProfile
 * @property Category $category
 * @property Category $postCategory
 * @property TagPost[] $tagPost
 * @property Tag[] $tags
 * @property ActiveDataProvider $posts
 */
class Post extends BaseModel
{
    use CommentTrait;

    const POSITION_DEFAULT = 0;

    /** @var string */
    public $authorName;
    public $collapse;

    /** @var string */
    private $_url;

    const CACHE_DURATION = 0;
    const CACHE_TAG_POST = 'post';
    const CACHE_TAG_POST_AUTHOR = 'post-author';
    const CACHE_TAG_POST_AUTHOR_PROFILE = 'post-author-profile';
    const CACHE_TAG_POST_CATEGORY = 'post-category';
    const CACHE_TAG_LAST_POST = 'post-last';

    const COMMENT_OFF = 0;
    const COMMENT_ON = 1;

    /**
     * @param string $className
     * @return Post
     */
    public static function model($className = __CLASS__)
    {
        return new $className;
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
            'sluggableBehavior' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'sort',
                'groupAttributes' => [
                    'category_id'
                ],
            ],
            'taggableBehavior' => [
                'class' => Taggable::class,
                'name' => 'title'
            ],
            'delCacheModelBehavior' => [
                'class' => DelCacheModelBehavior::class,
                'tags' => [
                    Tag::CACHE_TAG_TAGS,
                    Tag::CACHE_TAG_TAG_CLOUD,
                    Category::CACHE_TAG_CATEGORY,
                    self::CACHE_TAG_POST,
                ]
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
            [['anons', 'content'], 'string'],
            [['category_id', 'author_id'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],

            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISH]],

            [['sort', 'is_comment'], 'integer'],
            ['sort', 'default', 'value' => self::POSITION_DEFAULT],
            [['tagNames'], 'safe']
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
            'slug' => Module::t('module', 'Alias'),
            'anons' => Module::t('module', 'Anons'),
            'content' => Module::t('module', 'Content'),
            'category_id' => Module::t('module', 'Category'),
            'author_id' => Module::t('module', 'Author'),
            'authorName' => Module::t('module', 'Author'),
            'created_at' => Module::t('module', 'Created'),
            'updated_at' => Module::t('module', 'Updated'),
            'status' => Module::t('module', 'Status'),
            'sort' => Module::t('module', 'Sort'),
            'is_comment' => Module::t('module', 'Is Comment'),
            'tagNames' => Module::t('module', 'Tags')
        ];
    }

    /**
     * @return array
     */
    public static function getCommentsArray()
    {
        return [
            self::COMMENT_OFF => Module::t('module', 'Off'),
            self::COMMENT_ON => Module::t('module', 'On'),
        ];
    }

    /**
     * @return array
     */
    public static function getCommentLabelsArray()
    {
        return [
            self::COMMENT_OFF => 'default',
            self::COMMENT_ON => 'success'
        ];
    }

    /**
     * @return mixed
     */
    public function getCommentName()
    {
        return ArrayHelper::getValue(self::getCommentsArray(), $this->is_comment);
    }

    /**
     * Return <span class="label label-success">Active</span>
     * @return string
     */
    public function getCommentLabelName()
    {
        $name = ArrayHelper::getValue(self::getCommentLabelsArray(), $this->is_comment);
        return Html::tag('span', $this->getCommentName(), ['class' => 'label label-' . $name]);
    }

    /**
     * Set Status
     * @return int|string
     */
    public function setCommentStatus()
    {
        switch ($this->is_comment) {
            case self::COMMENT_ON:
                $this->is_comment = self::COMMENT_OFF;
                break;
            case self::COMMENT_OFF:
                $this->is_comment = self::COMMENT_ON;
                break;
            default:
                $this->is_comment = self::COMMENT_OFF;
        }
        return $this->is_comment;
    }

    /**
     * Генрирует URL.
     * Используйте $model->url вместо Yii::$app->urlManager->createUrl(...);
     * @return string
     */
    public function getUrl()
    {
        if ($this->_url === null) {
            if (Yii::$app->id === 'app-backend') {
                $this->_url = Url::to(['view', 'id' => $this->id]);
            } else if (($category = $this->postCategory) && $category !== null) {
                $this->_url = Url::to(['default/post', 'category' => $category->path, 'post' => $this->slug, 'prefix' => '.html']);
            } else {
                $this->_url = Url::to(['default/post', 'post' => $this->slug, 'prefix' => '.html']);
            }
        }
        return $this->_url;
    }

    /**
     * {@inheritdoc}
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostQuery(static::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public function getPostAuthor()
    {
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_POST, self::CACHE_TAG_POST_AUTHOR]]);
        $query = $this->getAuthor();
        return self::getDb()->cache(static function () use ($query) {
            return $query->one();
        }, self::CACHE_DURATION, $dependency);
    }

    public function getAuthorProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id'])->via('author');
    }

    public function getPostAuthorProfile()
    {
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_POST, self::CACHE_TAG_POST_AUTHOR_PROFILE]]);
        $query = $this->getAuthorProfile();
        return self::getDb()->cache(static function () use ($query) {
            return $query->one();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getPostCategory()
    {
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_POST, self::CACHE_TAG_POST_CATEGORY]]);
        $query = $this->getCategory();
        return self::getDb()->cache(static function () use ($query) {
            return $query->one();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @return ActiveQuery
     */
    public function getTagPost()
    {
        return $this->hasMany(TagPost::class, ['post_id' => 'id']);
    }

    /**
     * Tags to Post
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable(TagPost::tableName(), ['post_id' => 'id']);
    }

    /**
     * Get all published posts
     * @return ActiveDataProvider
     */
    public function getPosts()
    {
        $query = self::find()->published();
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                    'sort' => SORT_ASC,
                ]
            ]
        ]);
    }

    /**
     * Published Tags to Post
     * @return mixed
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function getTagsPublished()
    {
        /** @var $tags TagQuery */
        $tags = $this->getTags();
        $query = $tags->published();
        $dependency = new TagDependency(['tags' => [Tag::CACHE_TAG_TAGS]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->all();
        }, Tag::CACHE_DURATION, $dependency);
    }

    /**
     * Tags to string|array this post
     * @param bool $string if false return to array('id'=> 'title') tags, else return string title
     * @param bool $link if false return to string title, else return link
     * @param string $emptyString if no string tags return $emptyString
     * @return array|string
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function getStringTagsToPost($string = true, $link = false, $emptyString = '')
    {
        $items = [];
        if (($tags = $this->getTagsPublished()) && $tags !== null) {
            foreach ($tags as $tag) {
                $items[$tag->id] = $link === false ?
                    $tag->title :
                    Html::a($tag->title, ['default/tag', 'tag' => $tag->title], ['rel' => 'nofollow']);
            }
        }
        $itemsString = implode(', ', $items);
        $itemsString = !empty($itemsString) ? $itemsString : $emptyString;
        return $string === true ? $itemsString : $items;
    }

    /**
     * Category Title
     * @param bool $small
     * @return string
     * @throws Throwable
     */
    public function getCategoryTitlePath($small = true)
    {
        if ($this->category_id !== null && $this->category && $this->category !== null) {
            $parentCategories = Category::getAllParents($this->category->id);
            $str = '';
            foreach ($parentCategories as $parent) {
                $str .= $parent->title . '/';
            }
            $options = [
                'class' => $this->category->isPublished() ? 'publish' : 'draft'
            ];
            if ($small === true) {
                $result = Html::tag('span', $this->category->title, ArrayHelper::merge([
                    'title' => $str . $this->category->title,
                    'style' => 'cursor: help'
                ], $options));
            } else {
                $result = Html::tag('span', $str . $this->category->title, ArrayHelper::merge([], $options));
            }
            return $result;
        }
        return '-';
    }

    /**
     * Author Profile Name
     * @param bool $userProfileName
     * @return string|integer
     */
    public function getAuthorName($userProfileName = true)
    {
        $author = $this->postAuthor;
        $authorName = trim($author->username);
        if (($userProfileName === true)) {
            $profile = $this->postAuthorProfile;
            if ($profile !== null) {
                $firstName = $profile->first_name ?: '';
                $lastName = $profile->last_name ?: '';
                $name = trim($firstName) . ' ' . trim($lastName);
                $authorName = !empty($name) ? $name : $authorName;
            }
        }
        return $authorName ?: $this->author_id;
    }

    /**
     * @inheritDoc
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $user = Yii::$app->user;
                $this->author_id = $user->identity->id;
            }
            return true;
        }
        return false;
    }

    /**
     * @param int $limit
     * @param bool $published
     * @return mixed
     * @throws Throwable
     */
    public function findLastPost($limit = 5, $published = true)
    {
        $query = self::find();
        if ($published === true) {
            $query->published();
        }
        $query->orderBy(['id' => SORT_DESC]);
        $query->limit($limit);

        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_POST, self::CACHE_TAG_LAST_POST]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->all();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * All comments this entity
     * @return array|ActiveRecord[]
     */
    public function getCommentsData()
    {
        return $this->getComments()->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC])->all();
    }
}
