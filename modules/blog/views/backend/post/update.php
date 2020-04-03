<?php

use yii\helpers\Html;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Post */

echo $this->render('_base');
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="blog-backend-post-update">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->title) ?></h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <?= $this->render('form/_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> ' . Module::t('module', 'Save'), [
                'class' => 'btn btn-success',
                'form' => 'post-form'
            ]) ?>
        </div>
    </div>
</div>
