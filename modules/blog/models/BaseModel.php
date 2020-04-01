<?php

namespace modules\blog\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use modules\blog\Module;

/**
 * Class BaseModel
 * @package modules\blog\models
 */
class BaseModel extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISH = 1;

    //public $status;

    /**
     * Statuses
     * @return array
     */
    public static function getStatusesArray()
    {
        return [
            self::STATUS_DRAFT => Module::t('module', 'Draft'),
            self::STATUS_PUBLISH => Module::t('module', 'Publish')
        ];
    }

    /**
     * @return array
     */
    public static function getLabelsArray()
    {
        return [
            self::STATUS_DRAFT => 'default',
            self::STATUS_PUBLISH => 'success'
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return ArrayHelper::getValue(self::getStatusesArray(), $this->status);
    }

    /**
     * Return <span class="label label-success">Active</span>
     * @return string
     */
    public function getStatusLabelName()
    {
        $name = ArrayHelper::getValue(self::getLabelsArray(), $this->status);
        return Html::tag('span', $this->getStatusName(), ['class' => 'label label-' . $name]);
    }

    /**
     * Set Status
     * @return int|string
     */
    public function setStatus()
    {
        switch ($this->status) {
            case self::STATUS_PUBLISH:
                $this->status = self::STATUS_DRAFT;
                break;
            case self::STATUS_DRAFT:
                $this->status = self::STATUS_PUBLISH;
                break;
            default:
                $this->status = self::STATUS_DRAFT;
        }
        return $this->status;
    }
}
