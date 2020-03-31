<?php

namespace modules\blog\models;

use Yii;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
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
 * @property TagPost[] $blogTagPosts
 */
class Tags extends BaseModel
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
            [['created_at', 'updated_at', 'status'], 'integer'],
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
     * @return ActiveQuery
     */
    public function getTagPosts()
    {
        return $this->hasMany(TagPost::class, ['tag_id' => 'id']);
    }
}
