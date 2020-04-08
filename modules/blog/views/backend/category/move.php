<?php

use yii\helpers\Html;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model Category|CategoryTreeBehavior */

echo $this->render('_base');
$this->params['breadcrumbs'] = $model->getBreadcrumbs($this->params['breadcrumbs'], true);
$this->params['breadcrumbs'][] = Module::t('module', 'Move');
?>
<div class="blog-backend-category-move">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->title) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <?= $this->render('form/_move', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('<span class="glyphicon glyphicon-random"></span> ' . Module::t('module', 'Move'), [
                'class' => 'btn btn-success',
                'form' => 'form-move'
            ]) ?>
        </div>
    </div>
</div>