<?php

namespace modules\comment\models;

use yii\base\Model;
use modules\users\models\UserProfile;
use modules\users\models\User;
use modules\users\Module;

/**
 * Class SetPasswordForm
 * @package modules\comment\models
 */
class SetPasswordForm extends Model
{
    public $username;
    public $password;

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => Module::t('module', 'This username already exists.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => User::LENGTH_STRING_PASSWORD_MIN, 'max' => User::LENGTH_STRING_PASSWORD_MAX],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Module::t('module', 'Username'),
            'password' => Module::t('module', 'Password'),
        ];
    }

    /**
     * @param Comment $comment
     * @return User|null
     * @throws \yii\base\Exception
     */
    public function signup(Comment $comment)
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $comment->email;
            $user->setPassword($this->password);
            $user->status = User::STATUS_ACTIVE;
            $user->generateAuthKey();
            $user->generateEmailConfirmToken();
            if ($user->save()) {

                if (($profile = UserProfile::findOne(['user_id' => $user->id])) && $profile !== null) {
                    $profile->first_name = $comment->author;
                    $profile->save();
                }

                $comment->confirm = null;
                $comment->status = Comment::STATUS_APPROVED;
                $comment->save(false);
                return $user;
            }
        }
        return null;
    }
}
