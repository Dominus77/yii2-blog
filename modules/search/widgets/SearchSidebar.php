<?php

namespace modules\search\widgets;

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
            return $this->render('searchSidebar');
        }
        return '';
    }
}
