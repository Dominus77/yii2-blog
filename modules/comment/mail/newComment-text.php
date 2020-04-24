<?php

use yii\helpers\Url;
use modules\comment\Module;

/**
 * @var $this yii\web\View
 * @var $model modules\comment\models\Comment
 * @var $params array
 */

$model = isset($params['model']) ? $params['model'] : null;
$adminLink = isset($params['adminLink']) ? $params['adminLink'] : Yii::$app->urlManager->hostInfo . '/admin' . Url::to(['/comment/default/view', 'id' => $model->id]);
$formatter = Yii::$app->formatter;
?>

<?= Module::t('module', 'New comment awaiting moderation') ?>

<?= Module::t('module', 'ID') ?>: <?= $model->id ?>
<?= Module::t('module', 'Entity') ?>: <?= $model->entity ?>
<?= Module::t('module', 'Entity ID') ?>: <?= $model->entity_id ?>
<?= Module::t('module', 'Name') ?>: <?= $model->author ?>
<?= Module::t('module', 'Email') ?>: <?= $model->email ?>
<?= Module::t('module', 'Created') ?>: <?= $formatter->asDatetime($model->created_at, 'php:Y-m-d H:i:s') ?>
<?= Module::t('module', 'Status') ?>: <?= $model->getStatusName() ?>

<?= Module::t('module', 'Comment') ?>: <?= $model->comment ?>


<?= Module::t('module', 'Link to comment moderation') ?>: <?= $adminLink ?>
