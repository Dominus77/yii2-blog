<?php

namespace modules\blog\models;

use Yii;
use yii\db\ActiveQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use modules\users\models\User;
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
 *
 * @property User $author
 * @property Category $category
 * @property TagPost[] $blogTagPosts
 */
class Post extends BaseModel
{
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
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']]
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
            'created_at' => Module::t('module', 'Created'),
            'updated_at' => Module::t('module', 'Updated'),
            'status' => Module::t('module', 'Status')
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
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
    public function getTagPosts()
    {
        return $this->hasMany(TagPost::class, ['post_id' => 'id']);
    }
}
