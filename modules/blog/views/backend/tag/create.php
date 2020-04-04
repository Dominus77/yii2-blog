<?php

use yii\helpers\Html;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Tag */

echo $this->render('_base');
$this->params['breadcrumbs'][] = Module::t('module', 'Create');
?>
<div class="blog-backend-tag-create">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'New Tag')) ?></h3>
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
            <?= Html::submitButton('<span class="glyphicon glyphicon-plus"></span> ' . Module::t('module', 'Create'), [
                'class' => 'btn btn-success',
                'form' => 'tag-form'
            ]) ?>
        </div>
    </div>
</div>
