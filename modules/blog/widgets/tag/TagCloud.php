<?php

namespace modules\blog\widgets\tag;

use yii\db\Exception;
use yii\helpers\Html;
use yii\bootstrap\Widget;
use modules\blog\models\Tag;

/**
 * Class TagCloud
 * @package modules\blog\widgets\tag
 */
class TagCloud extends Widget
{
    public $limit = 20;

    /**
     * @return string|void
     * @throws Exception
     */
    public function run()
    {
        $tags = Tag::model()->findTagWeights($this->limit);
        foreach ($tags as $tag => $weight) {
            echo Html::a(Html::tag('span', $tag, ['style' => "font-size:{$weight}pt"]), ['default/tag', 'tag' => $tag], ['rel' => 'nofollow']) . ' ' . PHP_EOL;
        }
    }
}
