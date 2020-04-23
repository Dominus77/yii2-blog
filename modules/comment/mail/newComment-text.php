<?php

/**
 * @var $this yii\web\View
 * @var $model modules\comment\models\Comment
 * @var $params array
 */

use modules\comment\Module;

$commentLink = Yii::$app->urlManager->hostInfo.'/admin/comment';
if(isset($params['request'])) {
    $commentLink = $params['request'];
}
?>

Новый комментарий ожидает модерации
<?= $commentLink ?>
