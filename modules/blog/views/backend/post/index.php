<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\widgets\LinkPager;
use modules\blog\models\Post;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $searchModel modules\blog\models\search\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo $this->render('_base', ['link' => false]);
?>
<div class="blog-backend-post-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'Posts')) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left">
                <?= common\widgets\PageSize::widget([
                    'label' => '',
                    'defaultPageSize' => Post::getDefaultPageSize(),
                    'sizes' => Post::getSizes(),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]) ?>
            </div>
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
                'filterSelector' => 'select[name="per-page"]',
                'layout' => '{items}',
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover',
                ],
                'columns' => [
                    ['class' => SerialColumn::class],
                    'title',
                    'slug',
                    [
                        'attribute' => 'author_id',
                        'value' => static function (Post $model) {
                            return $model->getAuthorName();
                        }
                    ],
                    [
                        'attribute' => 'category_id',
                        'value' => static function (Post $model) {
                            return $model->category->title;
                        }
                    ],
                    'position',
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Post $model) {
                            return Post::getFormatData($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Html::activeDropDownList($searchModel, 'status', $searchModel->statusesArray, [
                            'class' => 'form-control',
                            'prompt' => Module::t('module', '- all -'),
                            'data' => [
                                'pjax' => true,
                            ],
                        ]),
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            return $model->getStatusLabelName();
                        }
                    ],
                    [
                        'class' => ActionColumn::class,
                        'contentOptions' => [
                            'class' => 'action-column',
                            'style' => 'width: 90px'
                        ],
                    ]
                ]
            ]) ?>
        </div>
        <div class="box-footer">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'registerLinkTags' => true,
                'options' => [
                    'class' => 'pagination pagination-sm no-margin pull-right',
                ]
            ]) ?>
        </div>
    </div>
</div>
