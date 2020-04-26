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
<div class="email-confirm-email">
    <p><?= Module::t('module', 'Hello') ?>!</p>
    <p>
        <?= Module::t('module', 'You or someone on your behalf indicated the address of this mailbox on {: siteName}. If it wasn’t you, then just ignore this letter, otherwise click on the link below.', [
            ':siteName' => Html::a(Html::encode(Yii::$app->name), Yii::$app->urlManager->hostInfo)
        ]) ?>
    </p>
    <p>
        <?= Module::t('module', 'Please follow the link to confirm your email address: {:confirmLink}', [
            ':confirmLink' => Html::a(Html::encode($params['confirmLink']), $params['confirmLink'])
        ]) ?>
    </p>
    <p>
        <?= Module::t('module', 'Best regards, administration {:siteName}', [
            ':siteName' => Html::a(Html::encode(Yii::$app->name), Yii::$app->urlManager->hostInfo)
        ]) ?>
    </p>
    <p>
        <small><?= Module::t('module', 'This letter is generated automatically and does not require a response.') ?></small>
    </p>
</div>
