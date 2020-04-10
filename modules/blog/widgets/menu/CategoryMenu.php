<?php

namespace modules\blog\widgets\menu;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\Widget;
use yii\widgets\Menu;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\Module;

/**
 * Class CategoryMenu
 * @package modules\blog\widgets\menu
 */
class CategoryMenu extends Widget
{
    public $status = true;
    public $title;
    public $items = [];
    public $options = ['class' => 'menu'];
    public $panelOptions = ['class' => 'panel panel-default'];
    public $encodeLabels = false;
    public $activateParents = true;
    public $icon;


    /**
     * @inheritDoc
     */
    public function init()
    {
        $icon = $this->icon ?: Html::tag('span', '', ['class' => 'glyphicon glyphicon-folder-open']) . ' ';
        $this->title = $this->title ?: $icon . Module::t('module', 'Categories');
        $this->items = $this->items ?: $this->getMenuItems();
        if (!is_array($this->items) || empty($this->items)) {
            $this->status = false;
        }
        $this->id = $this->id ?: $this->getId();
    }

    /**
     * @return string|void
     * @throws Exception
     */
    public function run()
    {
        if ($this->status === true) {
            echo Html::beginTag('div', ArrayHelper::merge(['id' => $this->id], $this->panelOptions)) . PHP_EOL;
            echo Html::tag('div', $this->title . PHP_EOL, ['class' => 'panel-heading']) . PHP_EOL;
            echo Html::beginTag('div', ['class' => 'panel-body']) . PHP_EOL;
            echo Menu::widget([
                    'options' => $this->options,
                    'encodeLabels' => $this->encodeLabels,
                    'activateParents' => $this->activateParents,
                    'items' => array_filter($this->items)
                ]) . PHP_EOL;
            echo Html::endTag('div') . PHP_EOL;
            echo Html::endTag('div') . PHP_EOL;
        }
    }

    /**
     * @return array
     */
    protected function getMenuItems()
    {
        if ($this->status === true) {
            /** @var Category|CategoryTreeBehavior $model */
            $model = new Category();
            return $model->getMenuItems();
        }
        return [];
    }
}
