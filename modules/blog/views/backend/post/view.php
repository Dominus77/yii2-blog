<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use modules\blog\assets\BlogAsset;
use modules\blog\models\Post;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model Post */

echo $this->render('_base');
$this->params['breadcrumbs'][] = $model->title;

YiiAsset::register($this);
BlogAsset::register($this);
?>
<div class="blog-backend-post-view">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->title) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'title',
                    'slug',
                    [
                        'attribute' => 'anons',
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'content',
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'tagNames',
                        'value' => static function (Post $model) {
                            return $model->getStringTagsToPost(true, false, '-');
                        }
                    ],
                    [
                        'attribute' => 'category_id',
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            return $model->getCategoryTitlePath(false);
                        }
                    ],
                    'sort',
                    [
                        'attribute' => 'author_id',
                        'value' => static function (Post $model) {
                            return $model->getAuthorName();
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Post $model) {
                            return Post::getFormatData($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => static function (Post $model) {
                            return Post::getFormatData($model->updated_at);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            $title = $model->isPublish ? Module::t('module', 'Click to change status to draft') : Module::t('module', 'Click to change status to publish');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
                        }
                    ],
                    [
                        'attribute' => 'is_comment',
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            $title = $model->is_comment ? Module::t('module', 'Click to disable commenting') : Module::t('module', 'Click to enable commenting');
                            return Html::a($model->getCommentLabelName(), ['change-status-comment', 'id' => $model->id], ['title' => $title]);
                        }
                    ]
                ],
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
</div>
