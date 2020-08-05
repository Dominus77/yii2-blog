<?php

namespace modules\search\controllers\frontend;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use himiklab\yii2\search\Search;

/**
 * Class DefaultController
 * @package modules\search\controllers\frontend
 */
class DefaultController extends Controller
{
    const PAGE_SIZE = 10;

    /**
     * @param string $q
     * @return string
     */
    public function actionIndex($q = '')
    {
        /** @var Search $search */
        $search = Yii::$app->search;
        $searchData = $search->find($q); // Search by full index.
        //$searchData = $search->find($q, ['model' => 'page']); // Search by index provided only by model `page`.

        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchData['results'],
            'pagination' => [
                'pageSize' => self::PAGE_SIZE
            ],
        ]);

        return $this->render('index', [
                'hits' => $dataProvider->getModels(),
                'pagination' => $dataProvider->getPagination(),
                'query' => $searchData['query']
            ]
        );
    }
}
