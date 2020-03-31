<?php

use yii\web\View;
use yii\helpers\Html;
use modules\blog\Module;

/* @var $this View */

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-backend-default-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

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
        </div>
        <div class="box-footer"></div>
    </div>
</div>
