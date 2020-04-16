<?php

namespace modules\comment\grid;

use yii\grid\DataColumn as BaseDataColumn;

/**
 * Class DataColumn
 * @package modules\comment\grid
 */
class DataColumn extends BaseDataColumn
{
    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if (isset($model->depth) && $model->depth === 0) {
            $value = '-';
            $format = 'text';
            return $this->grid->formatter->format($value, $format);
        }
        return parent::renderDataCellContent($model, $key, $index);
    }
}
