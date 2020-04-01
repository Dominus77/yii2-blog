<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use modules\blog\models\Category;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $searchModel modules\blog\models\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Categories');
?>
<div class="blog-backend-category-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'Categories')) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right">
                <p>
                    <?= Html::a('<span class="fa fa-plus"></span> ', ['create'], [
                        'class' => 'btn btn-block btn-success',
                        'title' => Module::t('module', 'Create'),
                        'data' => [
                            'toggle' => 'tooltip',
                            'placement' => 'left',
                            'pjax' => 0,
                        ],
                    ]) ?>
                </p>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],

                    //'id',
                    'tree',
                    'lft',
                    'rgt',
                    'depth',
                    //'title',
                    [
                        'attribute' => 'title',
                        'value' => static function (Category $model) {
                            return $model->title;
                        }
                    ],
                    'position',
                    //'slug',
                    //'description:ntext',
                    'created_at:datetime',
                    //'updated_at',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Category $model) {
                            return $model->getStatusLabelName();
                        }
                    ],

                    ['class' => ActionColumn::class],
                ],
            ]) ?>
        </div>
        <div class="box-footer"></div>
    </div>
</div>
