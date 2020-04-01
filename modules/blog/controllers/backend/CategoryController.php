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
use creocoder\nestedsets\NestedSetsBehavior;

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
        /** @var NestedSetsBehavior|Category $model */
        $model = new Category();
        if (!empty(Yii::$app->request->post('Category'))) {
            $post = Yii::$app->request->post('Category');
            $model->title = $post['title'];
            $model->slug = $post['slug'];
            $model->description = $post['description'];
            $model->position = $post['position'];
            $model->status = $post['status'];
            $parent_id = $post['parentId'];
            $model->parentId = $parent_id;
            if ($model->validate()) {
                if (empty($parent_id)) {
                    $model->makeRoot();
                } else {
                    $parent = Category::findOne($parent_id);
                    $model->appendTo($parent);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
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
        /** @var NestedSetsBehavior|Category $model */
        $model = $this->findModel($id);
        //$parent_id = $model->parentId;
        //$model->parentId = $parent_id;

        if (!empty(Yii::$app->request->post('Category'))) {
            $post = Yii::$app->request->post('Category');
            $model->title = $post['title'];
            $model->slug = $post['slug'];
            $model->description = $post['description'];
            $model->position = $post['position'];
            $model->status = $post['status'];
            $parent_id = $post['parentId'];
            $model->parentId = $parent_id;
            if ($model->save()) {
                if (empty($parent_id)) {
                    if (!$model->isRoot()) {
                        $model->makeRoot();
                    }
                } else {
                    if ($model->id !== $parent_id) {
                        $parent = Category::findOne($parent_id);
                        $model->appendTo($parent);
                    }
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        /*if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }*/
        $model->parentId = $model->tree;
        return $this->render('update', [
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
