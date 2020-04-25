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

<?= Module::t('module', 'Hello {:name}', [':name' => $model->author]) ?>!

<?= Module::t('module', '{:datetime} You left a comment on the site {:siteName}. We inform you that the comment was successfully moderated.', [
    ':datetime' => $formatter->asDatetime($model->created_at, 'php: d mm Y, H:i'),
    ':siteName' => Yii::$app->name,
]) ?>

<?= Module::t('module', 'You can go to the comment link: {:link}', [
    ':link' => $params['frontendLinkEntityComment']
]) ?>

<?= Module::t('module', 'Best regards, administration {:siteName}', [
    ':siteName' => Yii::$app->name,
]) ?>
