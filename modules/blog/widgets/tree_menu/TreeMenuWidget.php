<?php

namespace modules\blog\widgets\tree_menu;

use Yii;
use yii\helpers\Html;
use modules\blog\models\Category;
use modules\blog\widgets\tree_menu\assets\TreeMenuAsset;

/**
 * Class TreeMenuWidget
 * @package modules\blog\widgets\tree_menu
 */
class TreeMenuWidget extends BaseTreeMenu
{
    public $status = true;
    public $id;

    public function init()
    {
        parent::init();
        $this->id = $this->id ?: $this->getId();
        $this->status = $this->status ? true : false;
        $this->registerAssets();
    }

    public function run()
    {
        if ($this->status) {
            if (is_array($tree = $this->getRenderTree())) {
                echo Html::beginTag('ul', ['id' => $this->id, 'class' => 'ul-treefree ul-dropfree']) . PHP_EOL;
                foreach ($tree as $items) {
                    echo $items . PHP_EOL;
                }
            }
        }
    }

    /**
     * Render List Tree
     * @return array
     */
    protected function getRenderTree()
    {
        $array = [];
        if ($query = $this->getData()) {
            $depth = $this->depthStart;
            $i = 0;
            $array[] = Html::beginTag('ul') . PHP_EOL;
            foreach ($query as $n => $category) {
                if ($category->depth === $depth) {
                    $array[] = $i ? Html::endTag('li') . PHP_EOL : '';
                } else if ($category->depth > $depth) {
                    $array[] = Html::beginTag('ul') . PHP_EOL;
                } else {
                    $array[] = Html::endTag('li') . PHP_EOL;
                    for ($i = $depth - $category->depth; $i; $i--) {
                        $array[] = Html::endTag('ul') . PHP_EOL;
                        $array[] = Html::endTag('li') . PHP_EOL;
                    }
                }
                $array[] = Html::beginTag('li') . PHP_EOL;
                $array[] = $this->getItemActive($category) . PHP_EOL;
                $depth = $category->depth;
                $i++;
            }
            $correct = $this->depthStart > 1 ? 1 : 0;
            for ($i = $depth - $correct; $i; $i--) {
                $array[] = Html::endTag('li') . PHP_EOL;
                $array[] = Html::endTag('ul') . PHP_EOL;
            }
            $array[] = $this->depthStart === 0 ? Html::endTag('li') . PHP_EOL : '';
            $array[] = $this->depthStart === 0 ? Html::endTag('ul') . PHP_EOL : '';
        }
        return $array;
    }

    /**
     * @param $data
     * @return string
     */
    private function getItemActive($data)
    {
        if (Yii::$app->request->get('category') === $data->slug) {
            return '<strong>' . $data->title . '</strong>';
        }
        return Html::a($data->title, ['default/category', 'category' => $data->slug], ['rel' => 'nofollow']);
    }

    /**
     * Register resource
     */
    private function registerAssets()
    {
        $view = $this->getView();
        TreeMenuAsset::register($view);
    }
}
