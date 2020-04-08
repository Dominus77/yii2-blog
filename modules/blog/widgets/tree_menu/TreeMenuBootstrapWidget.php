<?php

namespace modules\blog\widgets\tree_menu;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use modules\blog\models\Category;
use modules\blog\widgets\tree_menu\assets\TreeMenuBootstrapAsset;

/**
 * Class TreeMenuBootstrapWidget
 * @package modules\blog\widgets\tree_menu
 */
class TreeMenuBootstrapWidget extends BaseTreeMenu
{
    public $status = true;

    /**
     * @var array
     * @val openedClass => glyphicon-folder-open, glyphicon-chevron-right
     * @val closedClass => glyphicon-folder-close, glyphicon-chevron-down
     */
    public $jsOptions = [];

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
                echo Html::beginTag('ul', ['id' => $this->id]) . PHP_EOL;
                foreach ($tree as $items) {
                    echo $items . PHP_EOL;
                }
                echo Html::endTag('ul') . PHP_EOL;
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
                $itemActive = $this->getItemActive($category);
                foreach ($itemActive as $item) {
                    $array[] = $item . PHP_EOL;
                }
                $depth = $category->depth;
                $i++;
            }
            for ($i = $depth; $i; $i--) {
                $array[] = Html::endTag('li') . PHP_EOL;
                $array[] = Html::endTag('ul') . PHP_EOL;
            }
        }
        return $array;
    }

    /**
     * @param $data
     * @return array
     */
    private function getItemActive($data)
    {
        if (Yii::$app->request->get('category') === $data->slug) {
            $array[] = Html::beginTag('li', ['class' => 'selected']);
            $array[] = '<strong>' . $data->title . '</strong>';
            return $array;
        }
        $array[] = Html::beginTag('li');
        $array[] = Html::a($data->title, ['default/category', 'category' => $data->slug], ['rel' => 'nofollow']);
        return $array;
    }

    /**
     * @return string
     */
    protected function getTreeJsOptions()
    {
        $object = ArrayHelper::merge([], $this->jsOptions);
        return json_encode($object);
    }

    /**
     * Register resource
     */
    private function registerAssets()
    {
        $view = $this->getView();
        $treeId = $this->id;
        $options = $this->getTreeJsOptions();
        TreeMenuBootstrapAsset::register($view);
        $view->registerJs("
                    $('#$treeId').treed($options);
            ", View::POS_END);
    }
}
