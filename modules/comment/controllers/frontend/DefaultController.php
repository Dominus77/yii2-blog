<?php

namespace modules\comment\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\captcha\CaptchaAction;
use modules\comment\controllers\common\BaseController;
use modules\comment\models\Comment;

/**
 * Class DefaultController
 * @package modules\comment\controllers\frontend
 */
class DefaultController extends BaseController
{
    /*public function init()
    {
        parent::init();
        Yii::$app->on(__CLASS__, Comment::EVENT_CREATE_COMMENT_SUCCESS, function ($event) {
            return $this->messageSuccess();
        });
        Yii::$app->on(__CLASS__, Comment::EVENT_CREATE_COMMENT_ERROR, function ($event) {
            return $this->messageError();
        });
    }*/

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
     * @return Comment|string|Response
     */
    public function actionCreate()
    {
        $actionCreate = parent::actionCreate();
        $model = $actionCreate['model'];
        $result = $actionCreate['result'];
        $params = ['request' => Yii::$app->request->referrer];
        if ($result === true) {
            Comment::messageSuccess();
            $model->send($params);
        }
        if ($result === false) {
            Comment::messageError();
        }
        return $this->redirect($params['request']);
    }
}
