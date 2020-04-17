<?php

namespace modules\blog\controllers\backend;

use yii\web\Controller;
use yii\filters\AccessControl;
use modules\rbac\models\Permission;

/**
 * Class DefaultController
 * @package modules\blog\controllers\backend
 */
class DefaultController extends Controller
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
        return $this->render('index');
    }
}
