<?php

namespace modules\comment\services;

use Yii;
use modules\comment\models\Comment;
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

    /**
     * @param Comment $model
     * @return array
     */
    public function getParams(Comment $model)
    {
        /** @var $query \modules\blog\models\Post */
        $query = $model->entityQuery;
        $backendLink = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerBackend->baseUrl;
        $frontendLinkEntityComment = Yii::$app->urlManager->hostInfo . $this->normalizeUrl($query->getUrl('frontend')) . '#comment-' . $model->id;
        $backendLinkEntityComment = $backendLink . $this->normalizeUrl($query->getUrl('index')) . '#item-' . $model->id;
        return [
            'model' => $model, // Модель Comment
            'request' => Yii::$app->request->referrer,
            'backendLink' => $backendLink, // Ссылка на админку
            'backendLinkEntityComment' => $backendLinkEntityComment, // Ссылка на комментарий в сущности админки
            'frontendLinkEntityComment' => $frontendLinkEntityComment, // Ссылка на комментарий сущности на фронте
        ];
    }

    /**
     * @param $url
     * @return string|string[]|null
     */
    protected function normalizeUrl($url)
    {
        $url = str_replace(Yii::$app->urlManager->baseUrl . '/', '/', $url);
        return preg_replace('|%2F|i', '/', $url);
    }
}
