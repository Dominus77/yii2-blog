<?php

namespace modules\blog\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use paulzi\nestedsets\NestedSetsBehavior;
use paulzi\autotree\AutoTreeTrait;
use modules\blog\models\query\CategoryQuery;
use modules\blog\Module;
use yii\helpers\VarDumper;

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
 * @property int $parentId
 */
class Category extends BaseModel
{
    public $parentId;
    public $childrenList;
    public $typeMove;

    const TYPE_BEFORE = 'before';
    const TYPE_AFTER = 'after';

    use AutoTreeTrait;

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
        ];
    }

    /**
     * @param int|null $depth
     * @return \yii\db\ActiveQuery
     */
    public function getParents($depth = null)
    {
        return $this->autoTreeCall('getParents', ['al', 'ns'], [$depth]);
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

            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],

            ['position', 'integer'],
            ['position', 'default', 'value' => 0],

            [['description'], 'string'],
            [['title', 'slug'], 'string', 'max' => 255],

            [['parentId', 'childrenList', 'typeMove'], 'safe']
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
            'status' => Module::t('module', 'Status'),
            'childrenList' => Module::t('module', 'Children List'),
            'typeMove' => Module::t('module', 'Move Type')
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
    /*public function getParent()
    {
        return $this->parents(1)->one();
    }*/

    /**
     * Move types
     * @return array
     */
    public static function getMoveTypesArray()
    {
        return [
            self::TYPE_BEFORE => Module::t('module', 'Before'),
            self::TYPE_AFTER => Module::t('module', 'After'),
        ];
    }

    /**
     * Return Children node
     * @param int $nodeId
     * @return array
     */
    public static function getSelectList($nodeId)
    {
        /** @var $node NestedSetsBehavior|Category */
        if ($node = self::findOne(['id' => $nodeId])) {
            $tree = self::find();
            $tree->select('id, tree, title, lft');
            if ($node->depth !== 0) {
                $tree->andWhere(['tree' => $node->tree, 'depth' => $node->depth]);
            } else {
                $tree->andWhere(['depth' => $node->depth]);
            }
            $nodes = $tree->andWhere(['NOT IN', 'id', $node->id])
                ->orderBy('tree, lft')
                ->all();
            return ArrayHelper::map($nodes, 'id', 'title');
        }
        return [];
    }

    /**
     * Breadcrumbs
     * @param int $nodeId
     * @param array $breadcrumbs
     * @return array
     */
    public static function getBreadcrumbs($nodeId = 0, $breadcrumbs = [])
    {
        /** @var Category $node */
        $node = self::findOne(['id' => $nodeId]);
        $parents = $node->getParents()->all();
        $params = $breadcrumbs;
        foreach ($parents as $parent) {
            $params[] = ['label' => $parent->title, 'url' => ['view', 'id' => $parent->id]];
        }
        return $params;
    }

    /**
     * Get a full tree as a list, except the node and its children
     * @param int $nodeId node's ID
     * @return array array of node
     */
    public static function getTree($nodeId)
    {
        // don't include children and the node
        $children = [];
        if (!empty($nodeId)) {
            /** @var $tree NestedSetsBehavior */
            $tree = self::findOne(['id' => $nodeId]);
            $children = ArrayHelper::merge(
                $tree->getDescendants()->column(),
                [$nodeId]
            );
        }

        $rows = self::find()
            ->select('id, title, lft, depth')
            ->where(['NOT IN', 'id', $children])
            ->orderBy('tree, lft')
            ->all();

        $return = [];
        /** @var Category $row */
        foreach ($rows as $row) {
            $return[$row->id] = str_repeat('-', $row->depth) . ' ' . $row->title;
        }

        return $return;
    }
}
