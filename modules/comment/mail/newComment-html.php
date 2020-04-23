<?php

/**
 * @var $this yii\web\View
 * @var $model modules\comment\models\Comment
 * @var $params array
 */

use yii\helpers\Html;
use modules\comment\Module;

$commentLink = Yii::$app->urlManager->hostInfo.'/admin/comment';
if(isset($params['request'])) {
    $commentLink = $params['request'];
}
?>
<div class="email-confirm">
    <p>Новый комментарий ожидает модерации</p>
    <p><?= Html::a(Html::encode($commentLink), $commentLink) ?></p>
</div>
