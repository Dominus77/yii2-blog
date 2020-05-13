<?php

namespace modules\config\controllers;

use Yii;
use yii\base\Action;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\base\InvalidConfigException;
use modules\config\models\Config;
use modules\config\traits\ModuleTrait;
use modules\config\Module;

/**
 * Class DefaultController
 * @package modules\config\controllers
 */
class DefaultController extends Controller
{
    use ModuleTrait;

    /**
     * Access roles
     * @var array
     */
    protected $accessRoles = [];

    /**
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (empty($this->getModule()->params['accessRoles'])) {
            throw new InvalidConfigException(Module::t('module', 'You must specify the params[\'accessRoles\'] in the module settings.'));
        }
        $this->accessRoles = $this->getModule()->params['accessRoles'];
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => $this->getAccess()
        ];
    }

    /**
     * @return array
     */
    private function getAccess()
    {
        return [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => $this->accessRoles,
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionUpdate()
    {
        $settings = Config::find()->indexBy('id')->all();
        if (Model::loadMultiple($settings, Yii::$app->request->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $setting->save(false);
            }
            Yii::$app->session->setFlash('success', Module::t('module', 'Settings successfully saved.'));
        }

        return $this->render('update', [
            'model' => $settings,
        ]);
    }
}
