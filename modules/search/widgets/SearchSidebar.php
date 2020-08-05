<?php

namespace modules\search\widgets;

use modules\search\models\SearchForm;
use yii\base\Widget;

/**
 * Class SearchSidebar
 * @package modules\search\widgets
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
            $model = new SearchForm();
            return $this->render('searchSidebar', [
                'model' => $model
            ]);
        }
        return '';
    }
}
