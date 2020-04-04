<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\widgets\LinkPager;
use modules\blog\models\Tag;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $searchModel modules\blog\models\search\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo $this->render('_base', ['link' => false]);
?>
<div class="blog-backend-tag-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'Tags')) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left">
                <?= common\widgets\PageSize::widget([
                    'label' => '',
                    'defaultPageSize' => Tag::getDefaultPageSize(),
                    'sizes' => Tag::getSizes(),
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
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Tag $model) {
                            return Tag::getFormatData($model->created_at);
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
                        'value' => static function (Tag $model) {
                            return $model->getStatusLabelName();
                        }
                    ],
                    ['class' => ActionColumn::class],
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
