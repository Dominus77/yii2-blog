<?php

namespace modules\blog\grid;

use Closure;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\DataColumn;

/**
 * Class DataDetailColumn
 * @package modules\blog\grid
 */
class DataDetailColumn extends DataColumn
{
    /** @var string|Closure */
    public $detail;

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
}
