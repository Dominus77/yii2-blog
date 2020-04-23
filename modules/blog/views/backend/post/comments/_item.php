<?php

use yii\helpers\Html;
use yii\web\View;
use modules\blog\models\Post;
use modules\comment\models\Comment;
use modules\comment\Module;

/** @var $this View */
/** @var $model Comment */
/** @var $entity Post */
/** @var string $avatar */
/** @var $key int */

$borderColor = 'orange';
if ($model->isApproved) {
    $borderColor = 'green';
}
if ($model->isBlocked) {
    $borderColor = 'red';
}
?>
<div id="item-<?= $model->id ?>" class="item"
     style="border: 1px solid <?= $borderColor ?>; border-top: 3px solid <?= $borderColor ?>; margin-left: <?= ($model->depth - 1) * 6 ?>rem;">
    <div class="item-status pull-right">
        <?= $model->statusLabelName ?>
    </div>
    <div class="item-avatar pull-left">
        <img class="img-rounded" src="<?= $avatar ?>" alt="...">
    </div>
    <div class="item-author">
        <?= $model->author ?>
    </div>
    <div class="item-date">
        <small><?= Yii::$app->formatter->asDatetime($model->created_at, 'php: d mm Y, H:i:s') ?></small>
    </div>
    <div class="clearfix"></div>
    <div class="item-comment">
        <?= $model->comment ?>
    </div>
    <div class="item-tool">
        <?= Html::button('<span class="glyphicon glyphicon-comment"></span> ' . Module::t('module', 'Reply'), [
            'class' => 'btn-reply btn btn-primary btn-sm',
            'data' => [
                'id' => $model->id,
                'entityId' => $entity->id
            ]
        ]) ?>

        <?php if ($model->status !== Comment::STATUS_APPROVED) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> ' . Module::t('module', 'Approve'), [
                '/comment/default/approved',
                'id' => $model->id,
                '#' => 'item-' . $model->id
            ], ['class' => 'btn btn-success btn-sm']) ?>
        <?php } ?>

        <?php if ($model->status !== Comment::STATUS_BLOCKED) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ban-circle"></span> ' . Module::t('module', 'Block'), [
                '/comment/default/blocked',
                'id' => $model->id,
                '#' => 'item-' . $model->id
            ], ['class' => 'btn btn-danger btn-sm']) ?>
        <?php } ?>

        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('module', 'Delete'), [
            '/comment/default/delete',
            'id' => $model->id,
            '#' => 'detail-' . $key
        ], [
            'class' => 'btn btn-default btn-sm',
            'data' => [
                'method' => 'post',
                'confirm' => Module::t('module', 'Are you sure you want to delete this item?')
            ]
        ]) ?>
    </div>
    <div class="reply-form-container" id="form-container-<?= $model->id ?>"></div>
</div>
