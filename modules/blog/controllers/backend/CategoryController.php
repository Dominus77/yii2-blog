<?php

namespace modules\blog\controllers\backend;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use modules\rbac\models\Permission;
use modules\blog\models\Category;
use modules\blog\models\search\CategorySearch;
use yii\web\Response;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        if (($post = Yii::$app->request->post()) && $model->load($post) && $model->validate()) {
            if (empty($model->parentId)) {
                $model->makeRoot()->save();
            } else {
                $node = Category::findOne(['id' => $model->parentId]);
                $model->appendTo($node)->save();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /*if (empty($model->parentId)) {
            if (!$model->isRoot()) {
                $model->makeRoot()->save();
            } else {
                $model->save();
            }
        } else if ($model->id !== $model->parentId) {
            $node = Category::findOne(['id' => $model->parentId]);
            $model->appendTo($node)->save();
        } else {
            $model->save();
        }*/
        if (($post = Yii::$app->request->post()) && $model->load($post) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        //$model->parentId = $model->getParentId();
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
                $selectList = Category::getChildrenList($post['parent'], $post['id']);
                return [
                    'result' => $this->renderPartial('ajax/selectList', ['selectList' => $selectList]),
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

            if (empty($model->parentId)) { // Как корень
                if (!$model->isRoot()) {
                    $model->makeRoot()->save(false);
                }
            } else if ($model->id !== $model->parentId) { // Перемещаем в указанную ноду
                $node = Category::findOne(['id' => $model->parentId]);
                $model->appendTo($node)->save(false);
            }

            // Перемещаем в пределах узла
            if (!empty($model->childrenList)) {
                $moveModel = $this->findModel($model->id);
                $node = $this->findModel($model->childrenList);
                if ($model->typeMove === Category::TYPE_BEFORE) {
                    $moveModel->insertBefore($node)->save(false);
                }
                if ($model->typeMove === Category::TYPE_AFTER) {
                    $moveModel->insertAfter($node)->save(false);
                }
            }
            return $this->redirect(['index']);
        }

        $model->parentId = $model->getParentId();
        if ($select = $model->getPrevNodeId()) {
            $typeMove = Category::TYPE_AFTER;
        } else if ($select = $model->getNextNodeId()) {
            $typeMove = Category::TYPE_BEFORE;
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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
