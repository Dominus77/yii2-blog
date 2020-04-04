<?php

namespace modules\blog\models;

use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use modules\blog\models\query\TagQuery;
use modules\blog\Module;

/**
 * This is the model class for table "{{%blog_tags}}".
 *
 * @property int $id ID
 * @property string $title Title
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
            [['status'], 'integer'],
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

    /**
     * Posts to tag
     * @return ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['id' => 'post_id'])->via('tagPost');
    }
}
