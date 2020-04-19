<?php

namespace modules\blog\controllers\backend;

use Yii;
use modules\blog\models\search\CommentSearch;
use yii\web\Controller;
use modules\rbac\models\Permission;
use yii\filters\AccessControl;

/**
 * Class CommentController
 * @package modules\blog\controllers\backend
 */
class CommentController extends Controller
{
    /**
     * @inheritdoc
     * @return array
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
            ]
        ];
    }

    /**
     * Displays homepage.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}