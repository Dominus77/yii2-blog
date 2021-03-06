<?php

namespace modules\comment\models;

use modules\comment\services\SenderParams;
use Yii;
use Throwable;
use yii\base\Event;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use paulzi\nestedsets\NestedSetsBehavior;
use paulzi\autotree\AutoTreeTrait;
use modules\comment\services\Sender;
use modules\comment\traits\ModuleTrait;
use modules\comment\models\query\CommentQuery;
use modules\comment\Module;
use modules\users\models\User;

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
 * @property string $confirm Confirm Token
 * @property string $redirect Redirect URL
 *
 * @property int $rootId Root ID
 * @property int $parentId Parent ID
 * @property ActiveQuery|Comment $parent
 * @property ActiveQuery|Comment $next
 * @property ActiveQuery|Comment $prev
 * @property ActiveQuery|Comment[] $children
 * @property ActiveQuery $entityQuery
 * @property bool $isApproved Is Approved
 * @property bool $isBlocked Is Blocked
 * @property string $statusLabelName Status Label Name
 * @property string $url Url
 * @property object $entityData Object Entity
 */
class Comment extends ActiveRecord
{
    use AutoTreeTrait, ModuleTrait;

    const STATUS_WAIT = 0;
    const STATUS_APPROVED = 1;
    const STATUS_BLOCKED = 2;
    const TYPE_BEFORE = 'before';
    const TYPE_AFTER = 'after';
    const SCENARIO_GUEST = 'guest';
    const SCENARIO_REPLY = 'reply';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_VIEW = 'view';

    const CACHE_DURATION = 0;
    const CACHE_TAG_COMMENTS = 'comments';
    const CACHE_TAG_COMMENTS_GET_NODES = 'comments-get-nodes';
    const CACHE_TAG_GET_URL = 'comment-get-url';
    const CACHE_TAG_LAST_COMMENTS = 'last-comments';
    const CACHE_TAG_ENTITY_DATA = 'entity-data';
    const CACHE_TAG_COMMENTS_COUNT_WAIT = 'comments-count-wait';
    const CACHE_TAG_COMMENTS_COUNT_APPROVED = 'comments-count-approved';
    const CACHE_TAG_COMMENTS_COUNT_BLOCKED = 'comments-count-blocked';
    const CACHE_TAG_COMMENTS_COUNT_ENTITY_WAIT = 'comments-count-entity-wait';
    const CACHE_TAG_COMMENTS_AVATAR = 'comments-avatar';

    const EVENT_COMMENT_WAIT = 'comment-wait';
    const EVENT_COMMENT_APPROVED = 'comment-approved';
    const EVENT_COMMENT_BLOCKED = 'comment-blocked';

    const CONFIRM_EMAIL_SUCCESS = 'success';

    public $childrenList;
    public $typeMove;
    public $verifyCode;

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
            [['verifyCode'], 'required', 'on' => self::SCENARIO_GUEST],
            ['verifyCode', 'captcha', 'captchaAction' => Url::to('/comment/default/captcha'), 'on' => self::SCENARIO_GUEST],
            [['rootId', 'parentId', 'childrenList', 'typeMove', 'confirm', 'redirect'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_GUEST] = ['entity', 'entity_id', 'author', 'email', 'comment', 'verifyCode'];
        $scenarios[self::SCENARIO_REPLY] = ['entity', 'entity_id', 'author', 'email', 'comment'];
        return $scenarios;
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
            'author' => Module::t('module', 'Name'),
            'email' => Module::t('module', 'Email'),
            'comment' => Module::t('module', 'Comment'),
            'created_at' => Module::t('module', 'Created'),
            'updated_at' => Module::t('module', 'Updated'),
            'status' => Module::t('module', 'Status'),
            'Parent' => Module::t('module', 'Parent'),
            'childrenList' => Module::t('module', 'Children List'),
            'typeMove' => Module::t('module', 'Type Move'),
            'verifyCode' => Module::t('module', 'Verify Code'),
            'confirm' => Module::t('module', 'Confirm Token'),
            'redirect' => Module::t('module', 'Redirect URL')
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
     * @return ActiveQuery
     */
    public function getEntityQuery()
    {
        return $this->hasOne($this->entity, ['id' => 'entity_id']);
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
     * @TODO: Cache
     * Change status children node
     * @param integer $nodeId
     * @return bool|int
     */
    public static function changeStatusChildren($nodeId)
    {
        if ($node = self::findOne(['id' => $nodeId])) {
            $childrenId = ArrayHelper::getColumn($node->getDescendants()->all(), 'id');
            foreach ($childrenId as $itemId) {
                /** @var Comment $model */
                $model = self::findOne($itemId);
                if ($model->status === self::STATUS_WAIT) {
                    Yii::$app->trigger(self::EVENT_COMMENT_APPROVED, new Event(['sender' => $model]));
                }
            }
            return self::updateAll(['status' => $node->status], ['id' => $childrenId]);
        }
        return false;
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
     * @param string $value
     */
    public function setComment($value)
    {
        $this->comment = StringHelper::truncate($value, 30, ' ...');
    }

    /**
     * @return string
     */
    public function getComment()
    {
        $this->setComment($this->comment);
        return $this->comment;
    }

    /**
     * @TODO: Cache
     * Get a full tree as a list, except the node and its children
     * @param null $excludeNodeId
     * @return array
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

        $query = self::find()
            ->select('id, lft, depth, comment, entity')
            ->where(['NOT IN', 'id', $children])
            ->orderBy('entity, tree, lft');

        $rows = $query->all();

        $return = [];

        foreach ($rows as $row) {
            /** @var $row Comment */
            if ($row->depth === 0) {
                $return[$row->id] = $row->entity;
            } else {
                $return[$row->id] = str_repeat('-', $row->depth) . ' ' . $row->getComment();
            }
        }
        return $return;
    }

    /**
     * Get request data
     * @param bool|int $status
     * @param int $depthStart
     * @param bool $tree
     * @return mixed
     * @throws Throwable
     */
    public function getNodes($status = false, $depthStart = 0, $tree = true)
    {
        $query = self::find()->where('depth' . ' >=' . $depthStart);
        $query->andWhere(['entity' => $this->entity, 'entity_id' => $this->entity_id]);

        if (is_int($status)) {
            $query->andWhere(['status' => $status]);
        }

        if ($tree === true) {
            $query->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);
        } else {
            $query->orderBy(['lft' => SORT_ASC]);
        }

        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_COMMENTS, self::CACHE_TAG_COMMENTS_GET_NODES]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->all();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @param integer|null $nodeId
     * @param integer|null $unsetId
     * @return array
     */
    public static function getChildrenList($nodeId = null, $unsetId = null)
    {
        if ($nodeId !== null && ($node = self::findOne(['id' => $nodeId]))) {
            $children = $node->children;
            $childrenArray = ArrayHelper::map($children, 'id', static function ($model) {
                return $model->depth === 0 ? $model->entity :
                    $model->getComment();
            });
            if ($unsetId !== null) {
                unset($childrenArray[$unsetId]);
            }
            return $childrenArray;
        }
        return [];
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
     * @return string
     * @throws Throwable
     */
    public function getUrl()
    {
        /** @var ActiveRecord $entity */
        $entity = $this->entity;
        $query = $entity::find()->where(['id' => $this->entity_id]);
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_COMMENTS, self::CACHE_TAG_GET_URL]]);
        $model = self::getDb()->cache(static function () use ($query) {
            return $query->one();
        }, self::CACHE_DURATION, $dependency);
        $url = $model->getUrl();
        return $url . '#comment-' . $this->id;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws Throwable
     */
    public static function getLastComments($limit = 5)
    {
        $query = self::find()
            ->where(['status' => self::STATUS_APPROVED])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit);

        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_COMMENTS, self::CACHE_TAG_LAST_COMMENTS]]);
        return self::getDb()->cache(static function () use ($query) {
            return $query->all();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * @return mixed|null
     * @throws Throwable
     */
    public function getEntityData()
    {
        /** @var ActiveRecord $entity */
        $entity = $this->entity;
        if (class_exists($entity)) {
            $query = $entity::find()->where(['id' => $this->entity_id]);
            $dependency = new TagDependency(['tags' => [self::CACHE_TAG_COMMENTS, self::CACHE_TAG_ENTITY_DATA]]);
            return self::getDb()->cache(static function () use ($query) {
                return $query->one();
            }, self::CACHE_DURATION, $dependency);
        }
        return null;
    }

    /**
     * Set message sending email
     */
    public static function messageSendingEmail()
    {
        $msg = Module::t('module', 'A letter was sent to you to verify your mail address. Please check your inbox.');
        Yii::$app->session->setFlash('success', $msg);
    }

    /**
     * Set message success
     */
    public static function messageApproved()
    {
        $msg = Module::t('module', 'Thank! Your comment has been posted successfully.');
        Yii::$app->session->setFlash('success', $msg);
    }

    /**
     * Set message success
     */
    public static function messageSuccess()
    {
        $msg = Module::t('module', 'Your comment will be published after successful moderation.');
        Yii::$app->session->setFlash('success', $msg);
    }

    /**
     * Set message error
     */
    public static function messageError()
    {
        $msg = Module::t('module', 'An error has occurred! Failed to add comment.');
        Yii::$app->session->setFlash('error', $msg);
    }

    /**
     * Sending to email
     * @param SenderParams $senderParams
     * @return bool
     */
    public function send(SenderParams $senderParams)
    {
        return Sender::send(
            $senderParams->templates,
            $senderParams->from,
            $senderParams->to,
            $senderParams->subject,
            $senderParams->params
        );
    }

    /**
     * @return int|string|null
     */
    public function getAuthorId()
    {
        if (($model = User::findOne(['email' => $this->email])) && $model !== null) {
            return $model->id;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return self::getGravatar($this->email);
        //return Url::to(['/comment/default/file', 'filename' => 'defaultAvatar.jpg']);
    }
}
