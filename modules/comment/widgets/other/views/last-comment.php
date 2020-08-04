<?php

use yii\helpers\Html;
use modules\comment\models\Comment;

/** @var $this yii\web\View */
/** @var $comment Comment */
/** @var $title string */
?>

<li class="item">
    <h5 class="item-title"><?= Html::encode($title) ?></h5>
    <div class="info">
        <div class="item-data">
            <?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?>
        </div>
        <div class="item-author">
            <?= $comment->author ?>
        </div>
        <div class="item-content">
            <?= Html::a($comment->getComment(), $comment->url) ?>
        </div>
    </div>
</li>
