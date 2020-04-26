<?php

namespace modules\comment\controllers\common;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\base\Event;
use common\components\behaviors\DelCacheControllerBehavior;
use modules\comment\services\SenderParams;
use modules\comment\models\Comment;

/**
 * Class BaseController
 * @package modules\comment\controllers\common
 */
class BaseController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->on(Comment::EVENT_COMMENT_APPROVED, static function ($event) {
            /** @var Comment $model */
            $model = $event->sender;
            $senderParams = new SenderParams();
            $senderParams->setSenderApprove($senderParams->getParams($model));
            return $model->send($senderParams);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'delCacheControllerBehavior' => [
                'class' => DelCacheControllerBehavior::class,
                'actions' => ['create', 'update', 'move', 'change-status', 'delete', 'approved', 'blocked', 'wait', 'confirm-email', 'set-password'],
                'tags' => [
                    Comment::CACHE_TAG_COMMENTS,
                    Comment::CACHE_TAG_LAST_COMMENTS,
                    Comment::CACHE_TAG_COMMENTS_COUNT_WAIT,
                    Comment::CACHE_TAG_COMMENTS_COUNT_APPROVED,
                    Comment::CACHE_TAG_COMMENTS_COUNT_BLOCKED,
                    Comment::CACHE_TAG_COMMENTS_COUNT_ENTITY_WAIT,
                    Comment::CACHE_TAG_COMMENTS_GET_NODES,
                    'post-all-comments',
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function actionCreate()
    {
        $model = new Comment();
        $result = false;
        if (($post = Yii::$app->request->post()) && $model->load($post) && $model->validate()) {
            if (empty($model->rootId)) {
                $result = $this->processFirst($model, $post);
            } else if (empty($model->parentId)) {
                $result = $this->processComment($model);
            } else {
                $result = $this->processAnswer($model);
            }
        }
        return ['model' => $model, 'result' => $result];
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionApproved($id)
    {
        $model = $this->findModel($id);
        $status = $model->status;
        $model->status = Comment::STATUS_APPROVED;
        if ($model->save(false)) {
            if ($status === Comment::STATUS_WAIT) {
                Yii::$app->trigger(Comment::EVENT_COMMENT_APPROVED, new Event(['sender' => $model]));
            }
            Comment::changeStatusChildren($model->id);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionBlocked($id)
    {
        $model = $this->findModel($id);
        $model->status = Comment::STATUS_BLOCKED;
        if ($model->save(false)) {
            Yii::$app->trigger(Comment::EVENT_COMMENT_BLOCKED, new Event(['sender' => $model]));
            Comment::changeStatusChildren($model->id);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionWait($id)
    {
        $model = $this->findModel($id);
        $model->status = Comment::STATUS_WAIT;
        if ($model->save(false)) {
            Yii::$app->trigger(Comment::EVENT_COMMENT_WAIT, new Event(['sender' => $model]));
            Comment::changeStatusChildren($model->id);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Comment to post is root
     * @param Comment $model
     * @param array $post
     * @return bool
     */
    protected function processFirst(Comment $model, array $post)
    {
        $model->status = Comment::STATUS_APPROVED;
        $model->entity_id = 0;
        if ($model->makeRoot()->save()) {
            $node = $model;
            $model = new Comment();
            if ($model->load($post) && $model->validate()) {
                return $model->appendTo($node)->save();
            }
        }
        return false;
    }

    /**
     * Comment to post
     * @param Comment $model
     * @return bool
     */
    protected function processComment(Comment $model)
    {
        $node = Comment::findOne(['id' => $model->rootId, 'entity' => $model->entity]);
        return $model->appendTo($node)->save();
    }

    /**
     * Answer to comment
     * @param Comment $model
     * @return bool
     */
    protected function processAnswer(Comment $model)
    {
        $node = Comment::findOne(['id' => $model->parentId, 'entity' => $model->entity]);
        return $model->appendTo($node)->save();
    }

    /**
     * Read file storage folder
     * <img src="<?= Url::to(['/comment/default/file', 'filename' => '1.png'])?>">
     * @throws NotFoundHttpException
     */
    public function actionFile()
    {
        $path = '@modules/comment/image';
        if ($file = Yii::$app->request->get('filename')) {
            $storagePath = Yii::getAlias($path);
            $response = Yii::$app->getResponse();
            $response->headers->set('Content-Type', 'image/jpg');
            $response->format = Response::FORMAT_RAW;
            $response->stream = fopen("$storagePath/$file", 'rb');
            return $response->send();
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}