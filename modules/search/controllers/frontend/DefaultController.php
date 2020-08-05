<?php

namespace modules\search\controllers\frontend;

use Yii;
use yii\web\Controller;
use modules\search\models\SearchForm;
use yii\web\NotFoundHttpException;

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
            $search = $model->search();
            $dataProvider = $search['dataProvider'];
            $searchData = $search['searchData'];
            $hits = $dataProvider->getModels();
            return $this->render('index', [
                    'hits' => $hits,
                    'pagination' => $dataProvider->getPagination(),
                    'query' => $searchData['query'],
                    'score' => Yii::$app->formatter->asDecimal($hits[0]->score, 2),
                    'model' => $model
                ]
            );
        }
        throw new NotFoundHttpException('404');
    }
}
