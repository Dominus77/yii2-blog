<?php

namespace modules\comment\controllers\common;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\components\behaviors\DelCacheControllerBehavior;
use modules\comment\models\Comment;

/**
 * Class BaseController
 * @package modules\comment\controllers\common
 */
class BaseController extends Controller
{
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
                'actions' => ['create', 'update', 'move', 'change-status', 'delete', 'approved', 'blocked', 'wait'],
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

    /**
     * @param Comment $model
     * @return array
     */
    protected function getParams(Comment $model)
    {
        return [
            'model' => $model,
            'request' => Yii::$app->request->referrer,
            'adminLink' => Yii::$app->urlManager->hostInfo . '/admin' . Url::to(['/blog/post/index', '#' => 'item-' . $model->id])
        ];
    }
}