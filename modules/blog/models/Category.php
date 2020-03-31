<?php

namespace modules\blog\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii2tech\ar\position\PositionBehavior;
use creocoder\nestedsets\NestedSetsBehavior;
use modules\blog\models\query\CategoryQuery;
use modules\blog\Module;

/**
 * This is the model class for table "{{%blog_category}}".
 *
 * @property int $id ID
 * @property int $tree Tree
 * @property int $lft L.Key
 * @property int $rgt R.Key
 * @property int $depth Depth
 * @property int $position Position
 * @property string $title Title
 * @property string $slug Alias
 * @property string $description Description
 * @property int $created_at Created
 * @property int $updated_at Updated
 * @property int $status Status
 *
 * @property Post[] $posts
 * @property Category $parent
 */
class Category extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'nestedSetsBehavior' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'tree'
            ],
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
                'positionAttribute' => 'position'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['position'], 'default', 'value' => self::STATUS_DRAFT],
            [['tree', 'lft', 'rgt', 'depth', 'position', 'status'], 'integer'],
            [['description'], 'string'],
            [['title', 'slug'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'tree' => Module::t('module', 'Tree'),
            'lft' => Module::t('module', 'L.Key'),
            'rgt' => Module::t('module', 'R.Key'),
            'depth' => Module::t('module', 'Depth'),
            'position' => Module::t('module', 'Position'),
            'title' => Module::t('module', 'Title'),
            'slug' => Module::t('module', 'Alias'),
            'description' => Module::t('module', 'Description'),
            'created_at' => Module::t('module', 'Created'),
            'updated_at' => Module::t('module', 'Updated'),
            'status' => Module::t('module', 'Status')
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['category_id' => 'id']);
    }

    /**
     * Get parent's ID
     * @return int
     */
    public function getParentId()
    {
        $parent = $this->parent;
        return $parent ? $parent->id : null;
    }

    /**
     * Get parent's node
     * @return array|ActiveRecord|null
     */
    public function getParent()
    {
        /** @var $this NestedSetsBehavior */
        return $this->parents(1)->one();
    }

    /**
     * Get a full tree as a list, except the node and its children
     * @param int $node_id node's ID
     * @return array array of node
     */
    public static function getTree($node_id = 0)
    {
        // don't include children and the node
        $children = [];

        if (!empty($node_id)) {
            /** @var $tree NestedSetsBehavior */
            $tree = self::findOne($node_id);
            $children = array_merge(
                $tree->children()->column(),
                [$node_id]
            );
        }

        $rows = self::find()
            ->select('id, title, slug, depth')
            ->where(['NOT IN', 'id', $children])
            ->orderBy('tree, lft, position')
            ->all();

        $return = [];
        /** @var Category $row */
        foreach ($rows as $row) {
            $return[$row->id] = str_repeat('-', $row->depth) . ' ' . $row->title;
        }

        return $return;
    }
}
