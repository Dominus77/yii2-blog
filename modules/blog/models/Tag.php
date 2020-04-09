<?php

namespace modules\blog\models;

use Yii;
use yii\db\Exception;
use yii\helpers\Html;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
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

    public function getTagsLinkString()
    {
        $model = self::find()->published()->all();
        $tags = '';
        foreach ($model as $item) {
            $size = $item->getCountToPosts() + 12;
            $tags .= Html::a(Html::tag('span', trim($item->title), ['style' => 'font-size:' . $size . 'px;']), ['default/tag', 'tag' => $item->title]) . ', ';
        }
        return rtrim($tags, ' ,');
    }

    /**
     * @return int
     */
    public function getCountToPosts()
    {
        return $this->getPosts()->count();
    }

    /**
     * Возвращает теги вместе с их весом
     * @param int $limit число возвращаемых тегов
     * @param bool $published если true то только для тегов имеющих статус "опубликовано"
     * @return array вес с индексом равным имени тега
     * @throws Exception
     */
    public function findTagWeights($limit = 20, $published = true)
    {
        $tags = [];
        $compare = '';
        $query = self::find();
        if ($published === true) {
            $query->published();
            $compare = ' WHERE status=' . self::STATUS_PUBLISH;
        }
        $models = $query->limit($limit)->all();
        $sizeRange = self::MAX_FONT_SIZE - self::MIN_FONT_SIZE;

        $minCount = log(Yii::$app->db->createCommand('SELECT MIN(frequency) FROM ' . self::tableName() . $compare)->queryScalar() + 1);
        $maxCount = log(Yii::$app->db->createCommand('SELECT MAX(frequency) FROM ' . self::tableName() . $compare)->queryScalar() + 1);

        $countRange = $maxCount - $minCount;

        foreach ($models as $model) {
            $tags[$model->title] = round(self::MIN_FONT_SIZE + (log($model->frequency + 1) - $minCount) * ($sizeRange / $countRange));
        }
        return $tags;
    }
}
