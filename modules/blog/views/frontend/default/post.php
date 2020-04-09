<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Menu;
use modules\blog\models\Post;
use modules\blog\models\Tag;
use modules\blog\widgets\tag\TagCloud;
use modules\blog\Module;

/** @var $this View */
/** @var $model Post */
/** @var $tags Tag */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['index']];
if (($category = $model->category) && $category !== null) {
    $this->params['breadcrumbs'] = $model->category->getBreadcrumbs($this->params['breadcrumbs'], true);
}
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="blog-frontend-default-post">
    <div class="row">
        <div class="col-md-3">
            <?php if ($category !== null) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Module::t('module', 'Menu') ?>
                    </div>
                    <div class="panel-body">
                        <?= Menu::widget([
                            'options' => ['class' => 'menu'],
                            'encodeLabels' => false,
                            'activateParents' => true,
                            'items' => array_filter($category->getMenuItems())
                        ]) ?>
                    </div>
                </div>
            <?php } ?>

            <noindex>
                <div class="tag-cloud panel panel-default">
                    <div class="panel-heading">
                        <?= Module::t('module', 'Tags') ?>
                    </div>
                    <div class="panel-body">
                        <?= TagCloud::widget(['limit' => 50]) ?>
                    </div>
                </div>
            </noindex>

        </div>
        <div class="col-md-9">

            <div class="content-container">
                <div class="header">
                    <h2><?= Html::encode($model->title) ?></h2>
                    <div class="info">
                        <span class="glyphicon glyphicon-calendar"></span> <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                        <span class="glyphicon glyphicon-user"></span> <?= $model->getAuthorName() ?>
                        <?php if ($category !== null) { ?>
                            <noindex>
                                <span class="glyphicon glyphicon-folder-open"></span> <?= Html::a($category->title, [$category->url], ['rel' => 'nofollow']) ?>
                            </noindex>
                        <?php } ?>
                    </div>
                </div>
                <div class="body">
                    <div class="content">
                        <?= $model->anons ?>
                        <?= $model->content ?>
                    </div>
                </div>
                <div class="footer">
                    <div class="info">
                        <?php if ($tags = $model->getLinkTagsToPost()) { ?>
                            <noindex>
                                <span class="glyphicon glyphicon-tags"></span> <?= Module::t('module', 'Tags') ?>
                                : <?= $tags ?>
                            </noindex>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
