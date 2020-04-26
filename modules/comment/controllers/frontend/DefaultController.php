<?php

namespace modules\comment\controllers\frontend;

use modules\comment\models\SetPasswordForm;
use modules\users\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\captcha\CaptchaAction;
use modules\comment\controllers\common\BaseController;
use modules\comment\models\Comment;
use modules\comment\services\SenderParams;

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
        // Проверяем авторизован ли пользователь
        if (Yii::$app->user->isGuest) {
            // генерируем токен и отправляем письмо
            $model->confirm = Yii::$app->security->generateRandomString();
            $model->redirect = Yii::$app->request->referrer;
            $model->save(false);

            $senderParams = new SenderParams();
            $params = $senderParams->getParams($model);
            $senderParams->setSenderConfirmEmail($params);
            $model->send($senderParams);

            Comment::messageSendingEmail();
            return $this->redirect($model->redirect);
        }

        $senderParams = new SenderParams();
        $params = $senderParams->getParams($model);
        $senderParams->setSenderCreate($params);

        if ($result === true) {
            $model->send($senderParams);
            Comment::messageSuccess();
        }
        if ($result === false) {
            Comment::messageError();
        }
        return $this->redirect($params['request']);
    }

    /**
     * @param string $token
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConfirmEmail($token)
    {
        if (($model = Comment::findOne(['confirm' => $token])) && $model !== null) {
            // Проверяем имеется ли пользователь с таким email
            if (($user = User::findOne(['email' => $model->email])) && $user !== null) {
                // Если имеется, авторизуем
                Yii::$app->user->login($user, 3600 * 24 * 30);
            } else {
                // Если нет то просим придумать пароль, регестрируем и авторизуем
                return $this->redirect(['set-password', 'token' => $model->confirm]);
            }
            $redirect = $model->redirect;
            $model->confirm = null;
            $model->redirect = null;
            $model->status = Comment::STATUS_WAIT;
            if ($model->save()) {

                $senderParams = new SenderParams();
                $params = $senderParams->getParams($model);
                $senderParams->setSenderCreate($params);
                $model->send($senderParams);

                Comment::messageSuccess();
                return $this->redirect($redirect);
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param string $token
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionSetPassword($token)
    {
        if (($comment = Comment::findOne(['confirm' => $token])) && $comment !== null) {
            $model = new SetPasswordForm();
            if (($post = Yii::$app->request->post()) && $model->load($post)) {
                $user = $model->signup($comment);
                if ($user !== null) {
                    Yii::$app->user->login($user, 3600 * 24 * 30);
                    $redirect = $comment->redirect;
                    $comment->status = Comment::STATUS_WAIT;
                    $comment->confirm = null;
                    $comment->redirect = null;
                    if ($comment->save()) {

                        $senderParams = new SenderParams();
                        $params = $senderParams->getParams($comment);
                        $senderParams->setSenderCreate($params);
                        $comment->send($senderParams);

                        Comment::messageSuccess();
                        return $this->redirect($redirect);
                    }
                }
            }
            return $this->render('set-password', [
                'model' => $model
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
