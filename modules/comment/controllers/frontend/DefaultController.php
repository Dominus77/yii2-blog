<?php

namespace modules\comment\controllers\frontend;

use Yii;
use yii\captcha\CaptchaAction;
use modules\comment\controllers\common\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\components\behaviors\DelCacheControllerBehavior;
use modules\comment\models\Comment;

/**
 * Class DefaultController
 * @package modules\comment\controllers\frontend
 */
class DefaultController extends BaseController
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
                    'add' => ['POST'],
                ],
            ],
            'delCacheControllerBehavior' => [
                'class' => DelCacheControllerBehavior::class,
                'actions' => ['add'],
                'tags' => [Comment::CACHE_TAG_COMMENTS]
            ]
        ];
    }

    /**
     * @return array|array[]
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor' => 0xF1F1F1,
                'foreColor' => 0xEE7600
            ]
        ];
    }

    /**
     * @return Response
     */
    public function actionAdd()
    {
        $model = new Comment();
        if (($post = Yii::$app->request->post()) && $model->load($post) && $model->validate()) {
            $msgSuccess = 'Спасибо! Ваш комментарий будет опубликован после успешной модерации.';
            $msgError = 'Произошла ошибка! Не удалось добавить комментарий.';
            if (empty($model->rootId)) {
                if ($this->processFirst($model, $post)) {
                    Yii::$app->session->setFlash('success', $msgSuccess);
                } else {
                    Yii::$app->session->setFlash('error', $msgError);
                }
            } else if (empty($model->parentId)) {
                if ($this->processComment($model)) {
                    Yii::$app->session->setFlash('success', $msgSuccess);
                } else {
                    Yii::$app->session->setFlash('error', $msgError);
                }
            } else if ($this->processAnswer($model)) {
                Yii::$app->session->setFlash('success', $msgSuccess);
            } else {
                Yii::$app->session->setFlash('error', $msgError);
            }
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
