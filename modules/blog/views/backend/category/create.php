<?php

use yii\helpers\Html;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Create');
?>
<div class="blog-backend-category-create">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'New Category')) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="box-footer"></div>
    </div>
</div>
