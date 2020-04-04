<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\widgets\LinkPager;
use modules\blog\models\Category;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $searchModel modules\blog\models\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo $this->render('_base', ['link' => false]);
?>
<div class="blog-backend-category-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'Categories')) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left">
                <?= common\widgets\PageSize::widget([
                    'label' => '',
                    'defaultPageSize' => Category::getDefaultPageSize(),
                    'sizes' => Category::getSizes(),
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
                    [
                        'attribute' => 'title',
                        'value' => static function (Category $model) {
                            return str_repeat('-', $model->depth) . ' ' . $model->title;
                        }
                    ],
                    'slug',
                    [
                        'attribute' => 'position',
                        'value' => static function (Category $model) {
                            return $model->isRoot() ? $model->position : '-';
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Category $model) {
                            return Category::getFormatData($model->created_at);
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
                        'value' => static function (Category $model) {
                            $title = $model->isPublish ? Module::t('module', 'Click to change status to draft') : Module::t('module', 'Click to change status to publish');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
                        }
                    ],
                    'depth',
                    [
                        'class' => ActionColumn::class,
                        'contentOptions' => [
                            'class' => 'action-column',
                            'style' => 'width: 90px'
                        ],
                        'template' => '{view} {move} {update} {delete}',
                        'buttons' => [
                            'move' => static function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-random"></span>', $url, [
                                    'title' => Module::t('module', 'Move'),
                                    'data' => [
                                        //'toggle' => 'tooltip',
                                        'pjax' => 0,
                                    ]
                                ]);
                            },
                        ]
                    ],
                ],
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
