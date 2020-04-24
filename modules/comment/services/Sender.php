<?php

namespace modules\comment\services;

use Yii;

/**
 * Class Sender
 * @package modules\comment\services
 */
class Sender
{
    /**
     * @param array $templates
     * @param array|string $from
     * @param array|string $to
     * @param string $subject
     * @param array $params
     * @return bool
     */
    public static function send($templates = [], $from = [], $to = [], $subject = '', $params = [])
    {
        $path = '@modules/comment/mail/';
        if (isset($templates['html'])) {
            $templates['html'] = $path . $templates['html'];
        }
        if (isset($templates['text'])) {
            $templates['text'] = $path . $templates['text'];
        }
        $mailer = Yii::$app->mailer;
        return $mailer->compose($templates, ['params' => $params])
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
