<?php

namespace modules\comment\models;

use modules\blog\models\Category;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use paulzi\nestedsets\NestedSetsBehavior;
use paulzi\autotree\AutoTreeTrait;
use modules\comment\models\query\CommentQuery;
use modules\comment\Module;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property int $id
 * @property int $tree Tree
 * @property int $lft L.Key
 * @property int $rgt R.Key
 * @property int $depth Depth
 * @property string $entity Entity
 * @property int $entity_id Entity ID
 * @property string $author Author
 * @property string $email Email
 * @property string $comment Comment
 * @property int $created_at Created
 * @property int $updated_at Updated
 * @property int $status Status
 *
 * @property int $rootId Root ID
 * @property int $parentId Parent ID
 */
class Comment extends ActiveRecord
{
    use AutoTreeTrait;

    const STATUS_WAIT = 0;
    const STATUS_APPROVED = 1;
    const STATUS_BLOCKED = 2;

    protected $rootId;
    public $parentId;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @return array
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
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_id', 'status'], 'integer'],
            [['entity', 'entity_id', 'author', 'email', 'comment'], 'required'],
            [['comment'], 'string'],
            [['entity', 'author'], 'string', 'max' => 255],
            ['email', 'email'],
            [['rootId', 'parentId'], 'safe']
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
            'entity' => Module::t('module', 'Entity'),
            'entity_id' => Module::t('module', 'Entity ID'),
            'author' => Module::t('module', 'Author'),
            'email' => Module::t('module', 'Email'),
            'comment' => Module::t('module', 'Comment'),
            'created_at' => Module::t('module', 'Created'),
            'updated_at' => Module::t('module', 'Updated'),
            'status' => Module::t('module', 'Status')
        ];
    }

    /**
     * @param int|null $depth
     * @return ActiveQuery
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
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(static::class);
    }

    /**
     * Statuses
     * @return array
     */
    public static function getStatusesArray()
    {
        return [
            self::STATUS_WAIT => Module::t('module', 'Wait'),
            self::STATUS_APPROVED => Module::t('module', 'Approved'),
            self::STATUS_BLOCKED => Module::t('module', 'Blocked')
        ];
    }

    /**
     * @return array
     */
    public static function getLabelsArray()
    {
        return [
            self::STATUS_WAIT => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_BLOCKED => 'danger'
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return ArrayHelper::getValue(self::getStatusesArray(), $this->status);
    }

    /**
     * Return <span class="label label-success">Active</span>
     * @return string
     */
    public function getStatusLabelName()
    {
        $name = ArrayHelper::getValue(self::getLabelsArray(), $this->status);
        return Html::tag('span', $this->getStatusName(), ['class' => 'label label-' . $name]);
    }

    /**
     * @return bool
     */
    public function getIsApproved()
    {
        return $this->status === static::STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function getIsWait()
    {
        return $this->status === static::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function getIsBlocked()
    {
        return $this->status === static::STATUS_BLOCKED;
    }

    /**
     * Set Status
     * @return int|string
     */
    public function setStatus()
    {
        switch ($this->status) {
            case self::STATUS_APPROVED:
            case self::STATUS_WAIT:
                $this->status = self::STATUS_BLOCKED;
                break;
            case self::STATUS_BLOCKED:
                $this->status = self::STATUS_APPROVED;
                break;
            default:
                $this->status = self::STATUS_WAIT;
        }
        return $this->status;
    }

    /**
     * Total column GridView
     * @param $provider
     * @param $fieldName
     * @return int
     */
    public static function pageTotal($provider, $fieldName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }
        return $total;
    }

    /**
     * Get root ID
     * @return int|mixed|null
     */
    public function getRootId()
    {
        $root = self::findOne(['lft' => 1, 'entity' => $this->entity]);
        return $root ? $root->id : null;
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
     * @param int $depth
     * @param string $itemsKey
     * @param string $getDataCallback
     * @return array
     */
    public function toNestedArray($depth = 1, $itemsKey = 'items', $getDataCallback = '')
    {
        $nodes = $this->getNodes($depth);
        $exportedAttributes = array_diff(array_keys($this->attributes), ['lft', 'rgt']);

        $trees = [];
        $stack = [];

        foreach ($nodes as $node) {
            if ($getDataCallback) {
                $item = $getDataCallback($node);
            } else {
                $item = $node->toArray($exportedAttributes);
                $item['options'] = ['class' => 'item_' . $node->getPrimaryKey()];
            }

            $item[$itemsKey] = [];
            $l = count($stack);

            while ($l > 0 && $stack[$l - 1]['depth'] >= $item['depth']) {
                array_pop($stack);
                $l--;
            }
            if ($l === 0) {
                // Assign root node
                $i = count($trees);
                $trees[$i] = $item;
                $stack[] = &$trees[$i];
            } else {
                // Add node to parent
                $i = count($stack[$l - 1][$itemsKey]);
                $stack[$l - 1][$itemsKey][$i] = $item;
                $stack[] = &$stack[$l - 1][$itemsKey][$i];
            }
        }
        return $trees;
    }

    /**
     * Get request data
     * @param int $depthStart
     * @param bool $tree
     * @return array|Category[]|ActiveRecord[]
     */
    protected function getNodes($depthStart = 0, $tree = true)
    {
        $query = self::find()->where('depth' . ' >=' . $depthStart);
        $query->andWhere(['entity' => $this->entity, 'entity_id' => $this->entity_id]);
        if ($this->status !== null && $this->status) {
            $query->andWhere(['status' => $this->status]);
        }
        if ($tree === true) {
            $query->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);
        } else {
            $query->orderBy(['lft' => SORT_ASC]);
        }
        return $query->all();
    }
}
