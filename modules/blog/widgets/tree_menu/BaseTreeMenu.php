<?php

namespace modules\blog\widgets\tree_menu;

use modules\blog\models\Category;
use yii\bootstrap\Widget;
use yii\helpers\Html;

/**
 * Class BaseTreeMenu
 * @package modules\blog\widgets\tree_menu
 */
class BaseTreeMenu extends Widget
{
    public $depthStart = 0;
    public $tree = true;

    /**
     * @return Category[]|array|bool
     */
    protected function getData()
    {
        $query = Category::find()
            ->where(['status' => Category::STATUS_PUBLISH])
            ->andWhere('depth >=' . $this->depthStart);
        if ($this->tree === true) {
            $query->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);
        } else {
            $query->orderBy(['lft' => SORT_ASC]);
        }
        return $query->all();
    }
}
