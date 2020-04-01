<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

\yii\web\YiiAsset::register($this);
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
                    'tree',
                    'lft',
                    'rgt',
                    'depth',
                    'position',
                    'title',
                    'slug',
                    'description:ntext',
                    'created_at:datetime',
                    'updated_at:datetime',
                    'status',
                ],
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
</div>
