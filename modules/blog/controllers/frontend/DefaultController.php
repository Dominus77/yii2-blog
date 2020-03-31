<?php

namespace modules\blog\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use modules\main\Module;

/**
 * Class DefaultController
 * @package modules\blog\controllers\frontend
 */
class DefaultController extends Controller
{
    /**
     * Displays homepage.
     * @return mixed|Response
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
