<?php

/**
 * @var $this yii\web\View
 * @var $model modules\comment\models\Comment
 * @var $params array
 */

use yii\helpers\Html;
use modules\comment\Module;

$model = isset($params['model']) ? $params['model'] : null;
$formatter = Yii::$app->formatter;
?>
<div class="email-approve-comment">
    <p><?= Module::t('module', 'Hello {:name}', [':name' => $model->author]) ?>!</p>
    <p>
        <?= Module::t('module', '{:datetime} You left a comment on the site {:siteName}. We inform you that the comment was successfully moderated.', [
            ':datetime' => $formatter->asDatetime($model->created_at, 'php: d mm Y, H:i'),
            ':siteName' => Html::a(Html::encode(Yii::$app->name), Yii::$app->urlManager->hostInfo),
        ]) ?>
    </p>
    <p>
        <?= Module::t('module', 'You can go to the comment link: {:link}', [
            ':link' => Html::a(Html::encode($params['frontendLinkEntityComment']), $params['frontendLinkEntityComment'])
        ]) ?>
    </p>
    <p>
        <?= Module::t('module', 'Best regards, administration {:siteName}', [
            ':siteName' => Html::a(Html::encode(Yii::$app->name), Yii::$app->urlManager->hostInfo),
        ]) ?>
    </p>
    <p>
        <small><?= Module::t('module', 'This letter is generated automatically and does not require a response.') ?></small>
    </p>
</div>
