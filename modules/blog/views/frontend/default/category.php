<?php

use yii\web\View;
use yii\helpers\Html;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\Module;
use yii\helpers\VarDumper;
use yii\widgets\Menu;

/* @var $this View */
/** @var $model Category|CategoryTreeBehavior */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['index']];
$this->params['breadcrumbs'] = $model->getBreadcrumbs($this->params['breadcrumbs']);
//VarDumper::dump($model->asJsTree(), 10, 1);
?>

<div class="blog-frontend-default-category">
    <h1><?= Html::decode($this->title) ?></h1>

    <?= Menu::widget([
        'items' => $model->getMenuItems()
    ]) ?>

    <?php
    VarDumper::dump($model->getUrl(), 10, 1);
    VarDumper::dump($model->title, 10, 1);
    VarDumper::dump($model->description, 10, 1);
    //VarDumper::dump($model->getBreadcrumbs(), 10, 1);
    ?>

</div>
