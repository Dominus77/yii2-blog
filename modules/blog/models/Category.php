<?php

namespace modules\blog\models;

use Yii;
use yii\data\ActiveDataProvider;
use paulzi\nestedsets\NestedSetsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use paulzi\autotree\AutoTreeTrait;
use modules\blog\models\query\CategoryQuery;
use modules\blog\Module;
use yii\helpers\Url;
use modules\blog\behaviors\CategoryTreeBehavior;

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
 *
 * @property string $path
 * @property string $url
 * @property bool $linkActive
 */
class Category extends BaseModel
{
    use AutoTreeTrait;

    public $parentId;
    public $childrenList;
    public $typeMove;
    private $_key;
    private $_url;

    const TYPE_BEFORE = 'before';
    const TYPE_AFTER = 'after';
    const POSITION_DEFAULT = 0;
    const CACHE_DURATION = 3600; // 1 час

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
            'categoryTreeBehavior' => [
                'class' => CategoryTreeBehavior::class,
                'status' => Yii::$app->id === 'app-frontend' ? self::STATUS_PUBLISH : '',
            ]
        ];
    }

    /**
     * @param int|null $depth
     * @return \yii\db\ActiveQuery
     */
    public function getParents($depth = null)
    {
        return $this->autoTreeCall('getParents', ['ns'], [$depth]);
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
                $this->_url = Url::to(['default/category', 'category' => $this->path]);
            }
        }
        return $this->_url;
    }

    /**
     * Возвращает список постов принадлежащих категории.
     * @return ActiveDataProvider
     */
    public function getPosts()
    {
        return new ActiveDataProvider([
            'query' => $this->hasMany(Post::class, ['category_id' => 'id'])
                ->where([
                    'status' => Post::STATUS_PUBLISH
                ]),
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                    'sort' => SORT_ASC,
                ]
            ],
        ]);
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->linkActive;
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
     * @return bool|int
     */
    public static function changeStatusChildren($nodeId)
    {
        if ($node = self::findOne(['id' => $nodeId])) {
            $childrenId = ArrayHelper::getColumn($node->getDescendants()->all(), 'id');
            return self::updateAll(['status' => $node->status], ['id' => $childrenId]);
        }
        return false;
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
     * All Parents to node ID
     * @param int $nodeId
     * @return array|Category[]|Tag[]|ActiveRecord[]
     */
    public static function getAllParents($nodeId = 0)
    {
        $cache = Yii::$app->cache;
        $key = [__CLASS__, __METHOD__, $nodeId];
        return $cache->getOrSet($key, static function () use ($nodeId) {
            /** @var Category $node */
            $node = self::findOne(['id' => $nodeId]);
            return $node->getParents()->all();
        }, static::CACHE_DURATION);
    }

    /**
     * Get a full tree as a list, except the node and its children
     * @param integer|null $excludeNodeId node's ID
     * @return array array of node
     */
    public static function getFullTree($excludeNodeId = null)
    {
        // don't include children and the node
        $children = [];
        if ($excludeNodeId !== null) {
            /** @var $tree NestedSetsBehavior */
            $tree = self::findOne(['id' => $excludeNodeId]);
            $children = ArrayHelper::merge(
                $tree->getDescendants()->column(),
                [$excludeNodeId]
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
