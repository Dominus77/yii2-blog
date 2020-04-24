<?php

/**
 * @var $this yii\web\View
 * @var $model modules\comment\models\Comment
 * @var $params array
 */

use yii\helpers\Url;
use yii\widgets\DetailView;
use modules\comment\models\Comment;
use modules\comment\Module;

$model = isset($params['model']) ? $params['model'] : null;
$adminLink = isset($params['adminLink']) ? $params['adminLink'] : Yii::$app->urlManager->hostInfo . '/admin' . Url::to(['/comment/default/view', 'id' => $model->id]);
$model->status = Comment::STATUS_WAIT;
?>
<div class="email-new-comment">
    <p><?= Module::t('module', 'New comment awaiting moderation') ?></p>
    <p>
        <?= DetailView::widget([
            'model' => $model,
            'options' => [
                'style' => 'text-align: left;',
            ],
            'attributes' => [
                'id',
                'entity',
                'entity_id',
                'author',
                'email:email',
                [
                    'attribute' => 'created_at',
                    'value' => static function ($model) {
                        $formatter = Yii::$app->formatter;
                        return $formatter->asDatetime($model->created_at, 'php:Y-m-d H:i:s');
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => static function ($model) {
                        return $model->getStatusName();
                    },
                ],
                [
                    'attribute' => 'comment',
                    'format' => 'raw',
                ]
            ]
        ]) ?>
    </p>
    <p>
        <?= Module::t('module', 'Link to comment moderation') ?>: <a href="<?= $adminLink ?>"><?= $adminLink ?></a>
    </p>
</div>
