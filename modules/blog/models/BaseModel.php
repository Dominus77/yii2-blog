<?php

namespace modules\blog\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use modules\blog\traits\ModuleTrait;
use modules\blog\Module;

/**
 * Class BaseModel
 * @package modules\blog\models
 * @property int $id [int(11)]  ID
 * @property string $title [varchar(255)]  Title
 * @property string $slug [varchar(255)]  Alias
 * @property string $anons Anons
 * @property string $content Content
 * @property int $category_id [int(11)]  Category
 * @property int $author_id [int(11)]  Author
 * @property int $created_at [int(11)]  Created
 * @property int $updated_at [int(11)]  Updated
 * @property int $status [smallint(6)]  Status
 * @property int $sort [int(11)]  Sort
 */
class BaseModel extends ActiveRecord
{
    use ModuleTrait;

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISH = 1;
    const SCENARIO_SET_STATUS = 'setStatus';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_post}}';
    }

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
     * @return bool
     */
    public function getIsPublish()
    {
        return $this->status === static::STATUS_PUBLISH;
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
