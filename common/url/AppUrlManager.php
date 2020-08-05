<?php

namespace common\url;

use yii\web\UrlManager;

/**
 * Class AppUrlManager
 * @package common\url
 */
class AppUrlManager extends UrlManager
{
    /**
     * @param array|string $params
     * @return string|string[]|null
     */
    public function createUrl($params)
    {
        return $this->fixPathSlashes(parent::createUrl($params));
    }

    /**
     * @param string $url
     * @return string|string[]|null
     */
    protected function fixPathSlashes($url)
    {
        return preg_replace('|%2F|i', '/', $url);
    }
}
