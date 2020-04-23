<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use modules\comment\models\Comment;
use modules\comment\Module;

/* @var $this yii\web\View */
/* @var $model Comment */

$this->title = Module::t('module', 'Comments');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->getComment();

YiiAsset::register($this);
?>
<div class="comment-backend-default-view">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->getComment()) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'entity',
                    'entity_id',
                    'author',
                    'email:email',
                    'comment:ntext',
                    [
                        'attribute' => 'created_at',
                        'value' => static function (Comment $model) {
                            return Comment::getFormatData($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => static function (Comment $model) {
                            return Comment::getFormatData($model->updated_at);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Comment $model) {
                            $title = $model->isApproved ? Module::t('module', 'Click to change status to blocked') : Module::t('module', 'Click to change status to approved');
                            return Html::a($model->getStatusLabelName(), ['change-status', 'id' => $model->id], ['title' => $title]);
                        },
                    ],
                ],
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-random"></span> ' . Module::t('module', 'Move'), ['move', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                    'params' => [
                        'scenario' => Comment::SCENARIO_VIEW
                    ]
                ],
            ]) ?>
        </div>
    </div>
</div>