<?php

use yii\web\View;
use modules\blog\models\Post;
use modules\comment\models\Comment;

/** @var $this View */
/** @var $model Post|Comment */

$comments = $model->getCommentsData();
?>

<div class="comment-list">
    <?php if ($comments) { ?>
        <?php foreach ($comments as $item) { ?>
            <?= $this->render('_item', [
                'model' => $item,
                'entity' => $model,
            ]) ?>
        <?php } ?>
    <?php } else { ?>
        <div class="item-blank">
            <p>Комментарии отсутствуют</p>
        </div>
    <?php } ?>
</div>
