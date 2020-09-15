<?php

namespace modules\users\models;

use yii\base\Model;
use yii\base\InvalidArgumentException;
use yii\db\StaleObjectException;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use modules\users\Module;
use Throwable;

/**
 * Class UpdatePasswordForm
 * @package modules\users\models
 * @property User $user
 */
class UserDeleteForm extends Model
{
    public $currentPassword;

    /**
     * @var User
     */
    private $_user;

    /**
     * UpdatePasswordForm constructor.
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        if (!$this->_user) {
            throw new InvalidArgumentException(Module::t('module', 'User not found.'));
        }
        parent::__construct($config);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['currentPassword'], 'required'],
            ['currentPassword', 'validateCurrentPassword', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    /**
     * @param string $attribute
     */
    public function validateCurrentPassword($attribute)
    {
        if (!empty($this->currentPassword) && !$this->hasErrors()) {
            $this->processValidatePassword($attribute);
        } else {
            $this->addError($attribute, Module::t('module', 'Not all fields are filled in correctly.'));
        }
    }

    /**
     * @param string $attribute
     */
    protected function processValidatePassword($attribute)
    {
        if ($attribute) {
            if (!$this->_user->validatePassword($this->$attribute)) {
                $this->addError($attribute, Module::t('module', 'Incorrect current password.'));
            }
        } else {
            $this->addError($attribute, Module::t('module', 'Enter your current password.'));
        }
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'currentPassword' => Module::t('module', 'Current Password'),
        ];
    }

    /**
     * @return bool|false|int
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function userDelete()
    {
        $user = $this->_user;
        /** @var $user SoftDeleteBehavior */
        return $user->softDelete();
    }
}
