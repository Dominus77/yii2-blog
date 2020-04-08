<?php

namespace modules\blog\url;

use yii\web\UrlManager;

/**
 * Class BlogUrlManager
 * @package modules\blog\url
 */
class BlogUrlManager extends UrlManager
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
