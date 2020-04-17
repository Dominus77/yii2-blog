<?php

use yii\helpers\Html;
use modules\comment\models\Comment;
use modules\comment\Module;

/** @var $this yii\web\View */
/** @var $model Comment */
/** @var $index integer */
/** @var $avatar string */

$icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-comment']);
?>
<div class="media-left">
    <img class="media-object img-rounded" src="<?= $avatar ?>" alt="<?= $avatar ?>">
</div>
<div class="media-body">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="link">
                <noindex>
                    <?= Html::a(Module::t('module', 'Link'), $model->url, ['rel' => 'nofollow']) ?>
                </noindex>
            </div>
            <div class="author"><?= $model->author ?></div>
            <div class="metadata">
                <span class="date"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php: d mm Y, H:i') ?></span>
            </div>
        </div>
        <div class="panel-body">
            <div class="media-text text-justify">
                <?= $model->comment ?>
            </div>
        </div>
        <div class="reply-container panel-footer" style="display:none;">
            <?= Html::button($icon . ' ' . Module::t('module', 'Reply'), [
                'id' => 'reply-button-' . $model->id,
                'class' => 'reply-button btn btn-info btn-sm',
                'data' => [
                    'id' => $model->id
                ],
                'onclick' => 'reply(this);',
            ]) ?>
            <div class="reply-form-container" id="reply-form-container-<?= $model->id ?>"></div>
        </div>
    </div>
</div>