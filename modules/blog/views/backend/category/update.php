<?php

use yii\helpers\Html;
use modules\blog\models\Category;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */

echo $this->render('_base');
$this->params['breadcrumbs'] = Category::getBreadcrumbs($model->id, $this->params['breadcrumbs']);
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module','Update');
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
        <div class="box-footer"></div>
    </div>
</div>
