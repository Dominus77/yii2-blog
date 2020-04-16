<?php

use yii\helpers\Html;
use modules\comment\Module;

/* @var $this yii\web\View */
/* @var $model modules\comment\models\Comment */

$this->title = Module::t('module', 'Comments');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'New Comment');
?>
<div class="comment-backend-default-create">

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode(Module::t('module', 'New Comment')) ?></h3>
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
