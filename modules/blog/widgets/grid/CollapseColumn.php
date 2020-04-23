<?php

namespace modules\blog\widgets\grid;

use Closure;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\DataColumn;

/**
 * Class CollapseColumn
 * @package modules\blog\widgets\grid
 */
class CollapseColumn extends DataColumn
{
    /** @var string|Closure */
    public $detail;
    /** @var string */
    public $collapse = 'collapse';

    /** @var array */
    public $detailOptions = [];

    /**
     * @param $model
     * @param $key
     * @param $index
     * @param $colspan
     * @return string
     */
    public function renderDetailCell($model, $key, $index, $colspan)
    {
        $options = ArrayHelper::merge(['colspan' => $colspan], $this->detailOptions);
        return Html::tag('td', $this->renderDetailCellContent($model, $key, $index), $options);
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return mixed|null
     */
    protected function renderDetailCellContent($model, $key, $index)
    {
        if ($this->detail !== null) {
            if (is_string($this->detail)) {
                return $this->detail;
            }
            return call_user_func($this->detail, $model, $key, $index, $this);
        }
        return null;
    }

    public function renderDataCell($model, $key, $index)
    {
        if ($this->contentOptions instanceof Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }
        $options['data-detail'] = 'detail-' . $key;
        Html::addCssClass($options, 'row-detail');
        Html::addCssStyle($options, 'cursor: pointer;');
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return Closure|mixed|string|null
     */
    public function getDataCellValue($model, $key, $index)
    {
        $this->attribute = $this->attribute ?: $this->collapse;

        if ($this->value !== null) {
            if (is_string($this->value)) {
                return $this->value;
            }
            return call_user_func($this->value, $model, $key, $index, $this);
        }

        return $this->renderContent();
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        $this->format = 'raw';
        $this->label = false;
        return Html::tag('span', '', [
            'class' => 'row-collapse glyphicon glyphicon-expand',
            'style' => 'font-size: 1.35em'
        ]);
    }
}
