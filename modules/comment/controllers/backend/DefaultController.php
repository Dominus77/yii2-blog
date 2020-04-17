<?php

namespace modules\comment\controllers\backend;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\components\behaviors\DelCacheControllerBehavior;
use modules\comment\models\Comment;
use modules\comment\models\search\CommentSearch;
use modules\rbac\models\Permission;

/**
 * Class DefaultController
 * @package modules\comment\controllers\backend
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::PERMISSION_MANAGER_COMMENTS]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'delCacheControllerBehavior' => [
                'class' => DelCacheControllerBehavior::class,
                'actions' => ['create', 'update', 'move', 'change-status', 'delete'],
                'tags' => [Comment::CACHE_TAG_COMMENTS]
            ]
        ];
    }

    /**
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comment model.
     * @param integer $id
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
     * Creates a new Comment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $model = new Comment();
        if (($post = Yii::$app->request->post()) && $model->load($post) && $model->validate()) {
            if (empty($model->parentId)) {
                $model->makeRoot()->save();
            } else {
                /** @var Comment $node */
                $node = Comment::findOne(['id' => $model->parentId]);
                $model->appendTo($node)->save();
            }
            // Перемещаем в пределах узла
            $model = $this->moveWithinNode($model);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Comment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Comment::changeStatusChildren($model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Return children list
     * @return array|Response
     */
    public function actionChildrenList()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($post = Yii::$app->request->post()) {
                $selectList = Comment::getChildrenList($post['parent'], $post['id']);
                $comment = $this->findModel($post['parent']);
                return [
                    'result' => $this->renderPartial('ajax/selectList', ['selectList' => $selectList]),
                    'entity' => $comment->entity,
                    'entityId' => $comment->entity_id,
                    'post' => $post
                ];
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Move node
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMove($id)
    {
        $model = $this->findModel($id);
        if (($post = Yii::$app->request->post()) && $model->load($post)) {

            if (empty($model->parentId)) { // Перемещаем как корень
                if (!$model->isRoot()) {
                    $model->makeRoot()->save(false);
                }
            } else if ($model->id !== $model->parentId) { // Перемещаем в указанный узел
                $node = $this->findModel($model->parentId);
                $model->appendTo($node)->save(false);
            }
            // Перемещаем в пределах узла
            $this->moveWithinNode($model);
            return $this->redirect(['index']);
        }

        $model->parentId = $model->getParentId();
        if ($select = $model->getPrevNodeId()) {
            $typeMove = Comment::TYPE_AFTER;
        } else if ($select = $model->getNextNodeId()) {
            $typeMove = Comment::TYPE_BEFORE;
        } else {
            $typeMove = null;
        }

        $model->childrenList = $select;
        $model->typeMove = $typeMove;
        return $this->render('move', [
            'model' => $model,
        ]);
    }

    /**
     * Change status
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus($id)
    {
        $model = $this->findModel($id);
        $model->setStatus();
        if ($model->save(false)) {
            Comment::changeStatusChildren($model->id);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->isRoot() ? $model->deleteWithChildren() : $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Move within a node
     * @param Comment $model
     * @return Comment
     * @throws NotFoundHttpException
     */
    protected function moveWithinNode(Comment $model)
    {
        if ($model !== null && !empty($model->childrenList)) {
            $moveModel = $this->findModel($model->id);
            $node = $this->findModel($model->childrenList);
            switch ($model->typeMove) {
                case Comment::TYPE_BEFORE:
                    $moveModel->insertBefore($node)->save(false);
                    break;
                case Comment::TYPE_AFTER:
                    $moveModel->insertAfter($node)->save(false);
                    break;
                default:
                    $moveModel->insertAfter($node)->save(false);
            }
        }
        return $model;
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
