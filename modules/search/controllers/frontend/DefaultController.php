<?php

namespace modules\search\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use modules\search\models\SearchForm;

/**
 * Class DefaultController
 * @package modules\search\controllers\frontend
 */
class DefaultController extends Controller
{
    const PAGE_SIZE = 10;

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $model = new SearchForm();
        if ($model->load(Yii::$app->request->get()) && $model->validate()) {
            $dataProvider = $model->search();
            $hits = $dataProvider->getModels();
            return $this->render('index', [
                    'hits' => $hits,
                    'pagination' => $dataProvider->getPagination(),
                    'score' => isset($hits[0]->score) ? Yii::$app->formatter->asDecimal($hits[0]->score, 2) : 0,
                    'model' => $model
                ]
            );
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
