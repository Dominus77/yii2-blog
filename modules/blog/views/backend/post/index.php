<?php

use yii\helpers\Html;

//use yii\grid\GridView;
use modules\blog\grid\GridView;
use modules\blog\grid\DataDetailColumn;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\widgets\LinkPager;
use modules\blog\assets\BlogAsset;
use modules\blog\models\Post;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $searchModel modules\blog\models\search\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo $this->render('_base', ['link' => false]);

BlogAsset::register($this);
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
                    [
                        'class' => SerialColumn::class,
                        'contentOptions' => [
                            'class' => 'data-column',
                            'style' => 'width: 50px'
                        ]
                    ],
                    [
                        'class' => DataDetailColumn::class,
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            return $model->title;
                        },
                        'detail' => function (Post $model) {
                            return $this->render('grid/comments', ['model' => $model]);
                        },
                    ],
                    'slug',
                    [
                        'class' => DataDetailColumn::class,
                        'attribute' => 'tagNames',
                        'value' => static function (Post $model) {
                            return $model->getStringTagsToPost(true, false, '-');
                        },
                    ],
                    [
                        'attribute' => 'authorName',
                        'value' => static function (Post $model) {
                            return $model->getAuthorName();
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'filter' => kartik\date\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_from',
                            'attribute2' => 'date_to',
                            'type' => kartik\date\DatePicker::TYPE_RANGE,
                            'separator' => '-',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'weekStart' => 1,
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ],
                        ]),
                        'format' => ['date', 'YYYY-MM-dd HH:mm:ss'],
                        'contentOptions' => [
                            'class' => 'data-column',
                            'style' => 'width: 250px'
                        ]
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Html::activeDropDownList($searchModel, 'status', $searchModel->statusesArray, [
                            'class' => 'form-control',
                            'prompt' => Module::t('module', '- all -'),
                            'data' => [
                                'pjax' => true
                            ]
                        ]),
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            $title = $model->isPublish ? Module::t('module', 'Click to change status to draft') : Module::t('module', 'Click to change status to publish');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
                        },
                        'contentOptions' => [
                            'class' => 'data-column',
                            'style' => 'width: 140px'
                        ],
                    ],
                    [
                        'attribute' => 'category_id',
                        'filter' => Html::activeDropDownList($searchModel, 'category_id', Post::getCategoriesTree(), [
                            'class' => 'form-control',
                            'prompt' => Module::t('module', '- all -'),
                            'data' => [
                                'pjax' => true
                            ]
                        ]),
                        'format' => 'raw',
                        'value' => static function (Post $model) {
                            return $model->getCategoryTitlePath();
                        }
                    ],
                    [
                        'attribute' => 'sort',
                        'contentOptions' => [
                            'class' => 'data-column',
                            'style' => 'width: 80px'
                        ]
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
