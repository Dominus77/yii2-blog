<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use modules\blog\models\Category;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model Category */

echo $this->render('_base');
$this->params['breadcrumbs'] = Category::getBreadcrumbs($model->id, $this->params['breadcrumbs']);
$this->params['breadcrumbs'][] = $model->title;

YiiAsset::register($this);
?>
<div class="blog-backend-category-view">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->title) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">

            <p>

            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'tree',
                        'value' => static function (Category $model) {
                            return $model->getTreeName();
                        }
                    ],
                    'title',
                    'slug',
                    [
                        'attribute' => 'position',
                        'value' => static function (Category $model) {
                            return $model->isRoot() ? $model->position : '-';
                        }
                    ],
                    'description:ntext',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Category $model) {
                            return $model->getStatusLabelName();
                        }
                    ],
                    'depth',
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Category $model) {
                            return Category::getFormatData($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => static function (Category $model) {
                            return Category::getFormatData($model->updated_at);
                        }
                    ]
                ],
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-random"></span> ' . Module::t('module', 'Move'), ['move', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
</div>
