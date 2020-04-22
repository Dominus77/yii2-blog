<?php

namespace modules\blog\widgets\grid;

use Closure;
use yii\grid\Column;
use yii\helpers\Html;
use yii\grid\GridView as BaseGridView;
use modules\blog\widgets\grid\assets\GridAsset;

/**
 * Class GridView
 * @package modules\blog\grid
 */
class GridView extends BaseGridView
{
    public $detailRowOptions = [];

    public function init()
    {
        parent::init();
        $this->registerAssets();
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        $cellDetail = [];
        $colspan = count($this->columns);
        /* @var $column Column|CollapseColumn */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
            if (isset($column->detail) && !empty($column->detail)) {
                $cellDetail[] = $column->renderDetailCell($model, $key, $index, $colspan);
            }
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string)$key;

        $cssClass = 'detail';
        $this->detailRowOptions['id'] = $cssClass . '-' . $options['data-key'];
        Html::addCssClass($this->detailRowOptions, $cssClass);

        $row = Html::tag('tr', implode('', $cells), $options);
        $detailRow = Html::tag('tr', implode('', $cellDetail), $this->detailRowOptions);

        return $cellDetail ? $row . PHP_EOL . $detailRow : $row;
    }

    /**
     * Register resource
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        GridAsset::register($view);
    }
}