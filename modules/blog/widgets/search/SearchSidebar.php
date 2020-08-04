<?php

namespace modules\blog\widgets\search;

use yii\base\Widget;

/**
 * Class SearchSidebar
 * @package modules\blog\widgets\search
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
