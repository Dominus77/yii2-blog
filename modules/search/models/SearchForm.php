<?php

namespace modules\search\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use modules\search\components\Search;
use modules\search\Module;

/**
 * Class SearchForm
 * @package modules\search\models
 */
class SearchForm extends Model
{
    const PAGE_SIZE = 10;

    /** @var string */
    public $q;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['q'], 'required'],
            [['q'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'q' => Module::t('module', 'Search')
        ];
    }

    /**
     * @return array
     */
    public function search()
    {
        /** @var Search $search */
        $search = Yii::$app->search;
        $searchData = $search->find($this->q);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchData['results'],
            'pagination' => [
                'pageSize' => self::PAGE_SIZE
            ],
        ]);

        return [
            'dataProvider' => $dataProvider,
            'searchData' => $searchData
        ];
    }
}
