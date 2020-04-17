<?php

use modules\comment\models\Comment;

/** @var $this yii\web\View */
/** @var $comments Comment[] */
/** @var $title string */
?>
<div class="comment-widget-last-comment">
    <ul>
        <?php foreach ($comments as $model) { ?>
            <li>
                <?= $model->getEntityData()->{$title} ?><br>
                <a rel="nofollow" href="<?= $model->url ?>">
                    <?= $model->author ?><br>
                    <?= Yii::$app->formatter->asDatetime($model->created_at, 'php: d mm Y, H:i') ?><br>
                    <?= $model->getComment() ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
