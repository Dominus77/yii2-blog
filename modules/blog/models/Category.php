<?php

namespace modules\blog\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use paulzi\nestedsets\NestedSetsBehavior;
use paulzi\autotree\AutoTreeTrait;
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
 * @property int $parentId
 * @property Category[] $children
 */
class Category extends BaseModel
{
    use AutoTreeTrait;

    public $parentId;
    public $childrenList;
    public $typeMove;

    const TYPE_BEFORE = 'before';
    const TYPE_AFTER = 'after';
    const POSITION_DEFAULT = 0;

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
            ['position', 'default', 'value' => self::POSITION_DEFAULT],

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
            'typeMove' => Module::t('module', 'Insert Type')
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
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISH;
    }

    /**
     * Root name
     * @return string
     */
    public function getTreeName()
    {
        $title = $this->title;
        if ($root = $this->getRoot()->one()) {
            $title = $root->title;
        }
        return $title;
    }

    /**
     * Format Date
     * @param integer $date
     * @return string
     * @throws InvalidConfigException
     */
    public static function getFormatData($date)
    {
        $formatter = Yii::$app->formatter;
        return $formatter->asDatetime($date, 'php:d-m-Y H:i:s');
    }

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
     * Return Prev node id
     * @return int|null
     */
    public function getPrevNodeId()
    {
        $prev = $this->prev;
        return $prev ? $prev->id : null;
    }

    /**
     * Return Next node id
     * @return int|null
     */
    public function getNextNodeId()
    {
        $next = $this->next;
        return $next ? $next->id : null;
    }

    /**
     * Change status children node
     * @param integer $nodeId
     * @return bool
     * @throws Exception
     */
    public static function changeStatusChildren($nodeId)
    {
        if ($node = self::findOne(['id' => $nodeId])) {
            $childrenId = ArrayHelper::getColumn($node->getDescendants()->all(), 'id');
            $connection = Yii::$app->db;
            $connection->createCommand()
                ->update(self::tableName(), ['status' => $node->status], ['id' => $childrenId])
                ->execute();
        }
        return true;
    }

    /**
     * @param integer|null $nodeId
     * @param integer|null $unsetId
     * @return array
     */
    public static function getChildrenList($nodeId = null, $unsetId = null)
    {
        if ($nodeId !== null && ($node = self::findOne(['id' => $nodeId]))) {
            $childrenArray = ArrayHelper::map($node->children, 'id', 'title');
            if ($unsetId !== null) {
                unset($childrenArray[$unsetId]);
            }
            return $childrenArray;
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
     * @param integer|null $nodeId node's ID
     * @return array array of node
     */
    public static function getTree($nodeId = null)
    {
        // don't include children and the node
        $children = [];
        if ($nodeId !== null) {
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
