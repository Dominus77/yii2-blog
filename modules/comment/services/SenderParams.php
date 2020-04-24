<?php

namespace modules\comment\services;

use Yii;
use modules\comment\Module;

/**
 * Class SenderParams
 * @package modules\comment\services
 */
class SenderParams
{
    public $templates = [];
    public $from = [];
    public $to = [];
    public $subject = '';
    public $params = [];

    /**
     * @param array $params
     */
    public function setSenderCreate($params = [])
    {
        $this->templates = [
            'html' => 'newComment-html',
            'text' => 'newComment-text',
        ];
        $this->from = [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
        $this->to = [Yii::$app->params['adminEmail']];
        $this->subject = Module::t('module', 'New comment') . ' ' . Yii::$app->name;
        $this->params = $params;
    }

    /**
     * @param array $params
     */
    public function setSenderApprove($params = [])
    {
        $model = $params['model'];
        $this->templates = [
            'html' => 'approveComment-html',
            'text' => 'approveComment-text',
        ];
        $this->from = [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
        $this->to = [$model->email];
        $this->subject = Module::t('module', 'Comment approved') . ' ' . Yii::$app->name;
        $this->params = $params;
    }
}
