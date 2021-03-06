<?php

namespace modules\blog\controllers\backend;

use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use modules\rbac\models\Permission;
use modules\blog\models\Post;
use modules\blog\models\search\PostSearch;
use modules\comment\models\Comment;
use modules\users\models\User;
use modules\blog\Module;
use Throwable;

/**
 * Class PostController
 * @package modules\blog\controllers\backend
 */
class PostController extends Controller
{
    /**
     * @return array|array[]
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::PERMISSION_MANAGER_POST]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $comment = new Comment();
        $user = Yii::$app->user;
        /** @var User $identity */
        $identity = $user->identity;
        $comment->author = $identity->username;
        $comment->email = $identity->email;
        $comment->entity = Post::class;
        $comment->scenario = Comment::SCENARIO_REPLY;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'comment' => $comment
        ]);
    }

    /**
     * Displays a single Post model.
     *
     * @param int|string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Post();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $session = Yii::$app->session;
            $session->setFlash('create', ['success', Module::t('module', 'Post successfully created.'), ['timeout' => 3000]]);
            $model->indexing();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int|string $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $session = Yii::$app->session;
            $session->setFlash('update', ['success', Module::t('module', 'Post successfully updated.'), ['timeout' => 3000]]);
            $model->indexing();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Change status
     *
     * @param int|string $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Post::SCENARIO_SET_STATUS;
        $model->setStatus();
        $model->save(false);
        $model->indexing();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * On/Off Commenting
     *
     * @param int|string $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatusComment($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Post::SCENARIO_SET_STATUS;
        $model->setCommentStatus();
        $model->save(false);
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int|string $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $session = Yii::$app->session;
        $session->setFlash('delete', ['success', Module::t('module', 'Post successfully delete.'), ['timeout' => 3000]]);
        $model = new Post();
        $model->indexing();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int|string $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
