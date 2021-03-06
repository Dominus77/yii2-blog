<?php

use yii\helpers\Html;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */

echo $this->render('_base');
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
            <?= $this->render('form/_create', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('<span class="glyphicon glyphicon-plus"></span> ' . Module::t('module', 'Create'), [
                'class' => 'btn btn-success',
                'form' => 'form-create'
            ]) ?>
        </div>
    </div>
</div>
