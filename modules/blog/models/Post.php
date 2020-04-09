<?php

namespace modules\blog\models;

use Yii;
use yii\base\InvalidConfigException;
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
use modules\blog\Module;

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
 *
 * @property User $author
 * @property Category $category
 * @property TagPost[] $tagPost
 * @property Tag[] $tags
 * @property ActiveDataProvider $posts
 */
class Post extends BaseModel
{
    const POSITION_DEFAULT = 0;

    /** @var string */
    public $authorName;
    /** @var string */
    private $_url;

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
            [['category_id', 'author_id', 'status'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],

            [['sort'], 'integer'],
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
            'tagNames' => Module::t('module', 'Tags')
        ];
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
            } else {
                if (($category = $this->category) && $category !== null) {
                    $this->_url = Url::to(['default/post', 'category' => $category->path, 'post' => $this->slug, 'prefix' => '.html']);
                } else {
                    $this->_url = Url::to(['default/post', 'post' => $this->slug, 'prefix' => '.html']);
                }
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

    public function getAuthorProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id'])->via('author');
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
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
        return new ActiveDataProvider([
            'query' => static::find()->published(),
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
     * @return array|Tag[]|ActiveRecord[]
     * @throws InvalidConfigException
     */
    public function getTagsPublished()
    {
        /** @var $tags TagQuery */
        $tags = $this->getTags();
        return $tags->published()->all();
    }

    /**
     * All Tags Array
     * @param bool|null $published
     * @return array
     */
    public function getAllTagsArray($published = true)
    {
        $tags = ($published === null) ? Tag::find()->all() : Tag::find()->published($published)->all();
        return ArrayHelper::map($tags, 'id', 'title');
    }

    /**
     * Tags to string|array this post
     * @param bool $string if false return to array('id'=> 'title') tags, else return string title
     * @param bool $link if false return to string title, else return link
     * @param string $emptyString if no string tags return $emptyString
     * @return array|string
     * @throws InvalidConfigException
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
        $author = $this->author;
        $authorName = trim($author->username);
        if (($userProfileName === true) && $author->profile !== null) {
            $profile = $author->profile;
            $firstName = $profile->first_name ?: '';
            $lastName = $profile->last_name ?: '';
            $name = trim($firstName) . ' ' . trim($lastName);
            $authorName = !empty($name) ? $name : $authorName;
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
}
