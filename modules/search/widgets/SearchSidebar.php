<?php

namespace modules\search\widgets;

use Yii;
use modules\search\models\SearchForm;
use yii\base\Widget;

/**
 * Class SearchSidebar
 * @package modules\search\widgets
 *
 * @property-read SearchForm $model
 */
class SearchSidebar extends Widget
{
    /** @var bool */
    public $status = true;

    /**
     * @return string
     */
    public function run()
    {
        if ($this->status === true) {
            return $this->render('searchSidebar', [
                'model' => $this->model
            ]);
        }
        return '';
    }

    /**
     * @return SearchForm
     */
    public function getModel()
    {
        $model = new SearchForm();
        $model->load(Yii::$app->request->get());
        return $model;
    }
}
