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
        $mailer = Yii::$app->mailer;
        return $mailer->compose($templates, ['params' => $params])
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
