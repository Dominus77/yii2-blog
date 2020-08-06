<?php

namespace modules\search\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
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
    public $query;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        $min = 3;
        $max = 255;
        $tooShort = Module::t('module', 'Request must contain at least {:num} characters', [':num' => $min]);
        $tooLong = Module::t('module', 'The request must contain a maximum of {:num} characters', [':num' => $max]);
        return [
            ['query', 'required', 'message' => Module::t('module', 'Enter your request')],
            ['query', 'filter', 'filter' => 'trim'],
            ['query', 'string', 'length' => [$min, $max], 'tooShort' => $tooShort, 'tooLong' => $tooLong,]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'query' => Module::t('module', 'Search')
        ];
    }

    /**
     * @return ArrayDataProvider
     */
    public function search()
    {
        /** @var Search $search */
        $search = Yii::$app->search;
        $searchData = $search->find(Html::encode($this->query));

        return new ArrayDataProvider([
            'allModels' => $searchData['results'],
            'pagination' => [
                'pageSize' => self::PAGE_SIZE
            ],
        ]);
    }
}
