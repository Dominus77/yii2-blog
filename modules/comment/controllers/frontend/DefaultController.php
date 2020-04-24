<?php

namespace modules\comment\controllers\frontend;

use modules\comment\services\SenderParams;
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
    /**
     * @return array|array[]
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'testLimit' => 3,
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
        /** @var Comment $model */
        $model = $actionCreate['model'];
        $result = $actionCreate['result'];

        $params = $this->getParams($model);
        if ($result === true) {
            $senderParams = new SenderParams();
            $senderParams->setSenderCreate($params);
            $model->send($senderParams);
            Comment::messageSuccess();
        }
        if ($result === false) {
            Comment::messageError();
        }
        return $this->redirect($params['request']);
    }
}
