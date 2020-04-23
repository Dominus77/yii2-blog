<?php

use yii\web\View;
use yii\helpers\Url;
use modules\blog\models\Post;
use modules\comment\models\Comment;

/** @var $this View */
/** @var $model Post|Comment */
/** @var $key int */

$comments = $model->getCommentsData();
$avatar = Url::to(['/comment/default/file', 'filename' => 'defaultAvatar.jpg']);
?>

<div class="comment-list">
    <?php if ($comments) { ?>
        <?php foreach ($comments as $item) { ?>
            <?= $this->render('_item', [
                'model' => $item,
                'entity' => $model,
                'avatar' => $avatar,
                'key' => $key
            ]) ?>
        <?php } ?>
    <?php } else { ?>
        <div class="item-blank">
            <p>Комментарии отсутствуют</p>
        </div>
    <?php } ?>
</div>
