<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use modules\blog\models\Tag;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Tag */

echo $this->render('_base');
$this->params['breadcrumbs'][] = $model->title;

YiiAsset::register($this);
?>
<div class="blog-backend-tag-view">
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
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Tag $model) {
                            return Tag::getFormatData($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => static function (Tag $model) {
                            return Tag::getFormatData($model->updated_at);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Tag $model) {
                            $title = $model->isPublish ? Module::t('module', 'Click to change status to draft') : Module::t('module', 'Click to change status to publish');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
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
