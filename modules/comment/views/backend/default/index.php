<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\helpers\StringHelper;
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
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'attribute' => 'comment',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return Html::tag('span', StringHelper::truncate($model->entity, 40, ' ...'), [
                                    'style' => 'font-weight: bold;'
                                ]);
                            }
                            return str_repeat('-', $model->depth - 1) . ' ' . StringHelper::truncate($model->comment, 30, ' ...');
                        }
                    ],
                    [
                        'attribute' => 'author',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return '-';
                            }
                            return $model->author;
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return '-';
                            }
                            return Html::mailto($model->email, $model->email);
                        }
                    ],
                    [
                        'attribute' => 'entity',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return '-';
                            }
                            return $model->entity;
                        }
                    ],
                    [
                        'attribute' => 'entity_id',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return '-';
                            }
                            return $model->entity_id;
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            if ($model->depth === 0) {
                                return '-';
                            }
                            return Comment::getFormatData($model->created_at);
                        }
                    ],
                    //'updated_at',
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
                            $title = $model->isApproved ? Module::t('module', 'Click to change status to block') : Module::t('module', 'Click to change status to approved');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
                        },
                        'contentOptions' => [
                            'class' => 'data-column',
                            'style' => 'width: 140px'
                        ],
                    ],

                    ['class' => ActionColumn::class],
                ],
            ]) ?>
        </div>
    </div>
</div>
