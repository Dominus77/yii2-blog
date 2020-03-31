<?php

namespace modules\blog\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use modules\rbac\models\Permission;
use modules\main\Module;

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
     * @return mixed|Response
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
