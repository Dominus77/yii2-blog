<?php

namespace modules\blog\controllers\frontend;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;

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
        $category = new Category();
        return $this->render('index', ['category' => $category]);
    }

    /**
     * @param $category
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($category)
    {
        $model = $this->findModel($category);
        return $this->render('category', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Category model based on its path value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param $path string
     * @return array|ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function findModel($path)
    {
        /** @var Category|CategoryTreeBehavior $model */
        $model = new Category();
        if (($category = $model->findByPath($path)) && $category !== null) {
            return $category;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
