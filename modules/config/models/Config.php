<?php

namespace modules\config\models;

use modules\config\Module;
use yii\db\ActiveRecord;
use modules\config\components\behaviors\CachedBehavior;

/**
 * This is the model class for table "{{%config}}".
 *
 * @property int $id ID
 * @property string $param Params
 * @property string $value Value
 * @property string $default Default
 * @property string $label Label
 * @property string $type Type
 */
class Config extends ActiveRecord
{
    /**
     * @var string
     */
    const CACHE_KEY = 'dConfig';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        parent::behaviors();
        return [
            'CachedBehavior' => [
                'class' => CachedBehavior::class,
                'cache_id' => [self::CACHE_KEY],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['param', 'type'], 'required'],
            [['value', 'default'], 'string'],
            [['param', 'type'], 'string', 'max' => 128],
            [['label'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'param' => Module::t('module', 'Params'),
            'value' => Module::t('module', 'Value'),
            'default' => Module::t('module', 'Default'),
            'label' => Module::t('module', 'Label'),
            'type' => Module::t('module', 'Type'),
        ];
    }
}
