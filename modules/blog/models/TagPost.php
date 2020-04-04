<?php

namespace modules\blog\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use modules\blog\Module;

/**
 * This is the model class for table "{{%blog_tag_post}}".
 *
 * @property int $tag_id ID Tag
 * @property int $post_id ID Post
 *
 * @property Post $post
 * @property Tag $tag
 */
class TagPost extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_tag_post}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag_id', 'post_id'], 'required'],
            [['tag_id', 'post_id'], 'integer'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['post_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => Module::t('module', 'ID Tag'),
            'post_id' => Module::t('module', 'ID Post')
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }
}
