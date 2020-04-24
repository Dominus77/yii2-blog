<?php

namespace modules\comment\controllers\backend;

use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use modules\comment\controllers\common\BaseController;
use modules\comment\models\Comment;
use modules\comment\models\search\CommentSearch;
use modules\rbac\models\Permission;
use Throwable;

/**
 * Class DefaultController
 * @package modules\comment\controllers\backend
 */
class DefaultController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
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
        ]);
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
     * @return Comment|string|Response
     */
    public function actionCreate()
    {
        $actionCreate = parent::actionCreate();
        $model = $actionCreate['model'];
        if (Yii::$app->request->post()) {
            $redirect = ['view', 'id' => $model->id];
            if ($model->scenario === Comment::SCENARIO_REPLY) {
                $redirect = Yii::$app->request->referrer . '#item-' . $model->id;
            }
            return $this->redirect($redirect);
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
     * @throws NotFoundHttpException
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
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if(isset($post['scenario']) && !empty($post['scenario'])) {
            $model->scenario = $post['scenario'];
        }
        $model->isRoot() ? $model->deleteWithChildren() : $model->delete();
        if ($model->scenario === Comment::SCENARIO_VIEW) {
            return $this->redirect(['index']);
        }
        return $this->redirect(Yii::$app->request->referrer);
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
}
