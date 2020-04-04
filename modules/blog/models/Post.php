<?php

namespace modules\blog\models;

use Yii;
use yii\db\ActiveQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii2tech\ar\position\PositionBehavior;
use modules\blog\models\query\TagQuery;
use modules\users\models\UserProfile;
use modules\users\models\User;
use modules\blog\models\query\PostQuery;
use modules\blog\Module;

/**
 * This is the model class for table "{{%blog_post}}".
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
 */
class Post extends BaseModel
{
    const POSITION_DEFAULT = 0;

    /**
     * Список ID тэгов, закреплённых за постом.
     * @var array
     */
    protected $tagsId = [];

    public $currentTag;

    public $authorName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_post}}';
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

            [['tagsId'], 'safe']
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
            'tags' => Module::t('module', 'Tags'),
            'currentTag' => Module::t('module', 'Tags'),
        ];
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
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->via('tagPost');
    }

    /**
     * Published Tags to Post
     * @return mixed
     */
    public function getTagsPublishedToPost()
    {
        /** @var $tags TagQuery */
        $tags = $this->getTags();
        return $tags->published()->all();
    }

    /**
     * Возвращает массив идентификаторов тэгов.
     */
    public function getTagsId()
    {
        return ArrayHelper::getColumn($this->tags, 'id');
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
     * @param bool $string
     * @return array|string
     */
    public function getStringTagsToPost($string = true)
    {
        $items = [];
        if (($tags = $this->getTagsPublishedToPost()) && $tags !== null) {
            foreach ($tags as $tag) {
                $items[] = $tag->title;
            }
        }
        $itemsString = implode(', ', $items);
        $itemsString = !empty($itemsString) ? $itemsString : '-';
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
            if ($small === true) {
                $result = Html::tag('span', $this->category->title, [
                    'title' => $str . $this->category->title,
                    'style' => 'cursor: help'
                ]);
            } else {
                $result = $str . $this->category->title;
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
        $authorName = $author->username;
        if (($userProfileName === true) && $author->profile !== null) {
            $profile = $author->profile;
            $firstName = $profile->first_name ?: '';
            $lastName = $profile->last_name ?: '';
            $name = trim(trim($firstName) . ' ' . trim($lastName));
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

    /**
     * @inheritdoc
     * @param bool $insert
     * @param array $changedAttributes
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        TagPost::deleteAll(['post_id' => $this->id]);
        $values = [];
        if (is_array($this->tagsId)) {
            foreach ($this->tagsId as $id) {
                $values[] = [$this->id, $id];
            }
            self::getDb()->createCommand()
                ->batchInsert(TagPost::tableName(), ['post_id', 'tag_id'], $values)->execute();
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
