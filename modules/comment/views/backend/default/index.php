<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use modules\comment\grid\DataColumn;
use modules\comment\models\search\CommentSearch;
use modules\comment\models\Comment;
use modules\comment\Module;

/* @var $this yii\web\View */
/* @var $searchModel CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Comments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-backend-default-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left">
                <?= common\widgets\PageSize::widget([
                    'label' => '',
                    'defaultPageSize' => Comment::getDefaultPageSize(),
                    'sizes' => Comment::getSizes(),
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
                'rowOptions' => static function ($model) {
                    $options = [];
                    if ($model->depth === 0) {
                        $options = [
                            'style' => 'background: #d9edf7;'
                        ];
                    }
                    return $options;
                },
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'attribute' => 'comment',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return Html::tag('span', $model->entity, [
                                    'style' => 'font-weight: bold;'
                                ]);
                            }
                            return str_repeat('-', $model->depth - 1) . ' ' . $model->getComment();
                        }
                    ],
                    [
                        'class' => DataColumn::class,
                        'attribute' => 'author',
                    ],
                    [
                        'class' => DataColumn::class,
                        'attribute' => 'email',
                        'format' => 'email',
                    ],
                    [
                        'class' => DataColumn::class,
                        'attribute' => 'entity',
                    ],
                    [
                        'class' => DataColumn::class,
                        'attribute' => 'entity_id',
                    ],
                    [
                        'class' => DataColumn::class,
                        'attribute' => 'created_at',
                        'value' => static function (Comment $model) {
                            return Comment::getFormatData($model->created_at);
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
                        'value' => static function (Comment $model) {
                            $title = $model->isApproved ? Module::t('module', 'Click to change status to blocked') : Module::t('module', 'Click to change status to approved');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
                        },
                        'contentOptions' => [
                            'class' => 'data-column',
                            'style' => 'width: 140px'
                        ],
                    ],
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
                                        'pjax' => 0,
                                    ]
                                ]);
                            },
                        ]
                    ]
                ],
            ]) ?>
        </div>
    </div>
</div>
