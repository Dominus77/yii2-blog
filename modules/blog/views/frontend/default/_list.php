<?php

use yii\web\View;
use yii\helpers\Html;
use modules\blog\models\Post;
use modules\blog\Module;

/** @var $this View */
/** @var $model Post */
?>

<div class="content-container">
    <div class="header">
        <h2><a href="<?= $model->url ?>"><?= Html::encode($model->title) ?></a></h2>
        <div class="info">
            <span class="glyphicon glyphicon-calendar"></span> <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
            <span class="glyphicon glyphicon-user"></span> <?= $model->getAuthorName() ?>
        </div>
    </div>
    <div class="body">
        <div class="content">
            <?= $model->anons ?>
        </div>
    </div>
    <div class="footer">
        <div class="info">
            <?php if (($category = $model->category) && $category !== null) { ?>
                <noindex>
                    <span class="glyphicon glyphicon-folder-open"></span> <?= Html::a($category->title, [$category->url], ['rel' => 'nofollow']) ?>
                </noindex>
            <?php } ?>
            <?php if ($tags = $model->getStringTagsToPost(true, true)) { ?>
                <noindex>
                    <span class="glyphicon glyphicon-tags"></span> <?= Module::t('module', 'Tags') ?>: <?= $tags ?>
                </noindex>
            <?php } ?>
        </div>
    </div>
</div>
