<?php

use yii\helpers\Html;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model Category|CategoryTreeBehavior */

echo $this->render('_base');
$this->params['breadcrumbs'] = $model->getBreadcrumbs($this->params['breadcrumbs'], true);
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="blog-backend-category-update">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->title) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <?= $this->render('form/_update', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> ' . Module::t('module', 'Save'), [
                'class' => 'btn btn-success',
                'form' => 'form-update'
            ]) ?>
        </div>
    </div>
</div>
