<?php

use yii\helpers\Html;
use modules\comment\models\Comment;

/** @var $this yii\web\View */
/** @var $comment Comment */
/** @var $title string */
?>

<li class="item">
    <h5 class="item-title"><?= Html::a(Html::encode($title), $comment->url) ?></h5>
    <div class="info">
        <div class="item-data">
            <?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?>
        </div>
        <div class="item-author">
            <?= $comment->author ?>
        </div>
        <div class="item-content">
            <?= $comment->getComment() ?>
        </div>
    </div>
</li>
