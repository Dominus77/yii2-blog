<?php

use yii\helpers\Html;
use yii\web\View;
use modules\blog\models\Post;

/** @var $this View */
/** @var $post Post */
?>
<li class="item">
    <h5 class="item-title"><?= Html::a($post->title, $post->url) ?></h5>
    <div class="info">
        <div class="item-data">
            <?= Yii::$app->formatter->asRelativeTime($post->created_at) ?>
        </div>
        <div class="item-author">
            <?= $post->getAuthorName() ?>
        </div>
    </div>
</li>
