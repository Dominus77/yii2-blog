<?php

use modules\comment\Module;

/**
 * @var $this yii\web\View
 * @var $model modules\comment\models\Comment
 * @var $params array
 */

$model = isset($params['model']) ? $params['model'] : null;
$formatter = Yii::$app->formatter;
?>

<?= Module::t('module', 'Hello') ?>!

<?= Module::t('module', 'You or someone on your behalf indicated the address of this mailbox on {: siteName}. If it wasn’t you, then just ignore this letter, otherwise click on the link below.', [
    ':siteName' => Yii::$app->urlManager->hostInfo,
]) ?>

<?= Module::t('module', 'Please follow the link to confirm your email address: {:confirmLink}', [
    ':confirmLink' => $params['confirmLink']
]) ?>

<?= Module::t('module', 'Best regards, administration {:siteName}', [
    ':siteName' => Yii::$app->name,
]) ?>

<?= Module::t('module', 'This letter is generated automatically and does not require a response.') ?>
